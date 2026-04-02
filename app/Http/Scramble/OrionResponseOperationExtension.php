<?php

namespace App\Http\Scramble;

use Dedoc\Scramble\Extensions\OperationExtension;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Reference;
use Dedoc\Scramble\Support\Generator\RequestBodyObject;
use Dedoc\Scramble\Support\Generator\Response;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Types\ArrayType;
use Dedoc\Scramble\Support\Generator\Types\ObjectType as GeneratorObjectType;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Dedoc\Scramble\Support\OperationExtensions\RulesExtractor\RulesToParameters;
use Dedoc\Scramble\Support\RouteInfo;
use Dedoc\Scramble\Support\Type\ObjectType as InferObjectType;
use Illuminate\Http\Request;
use Orion\Http\Controllers\Controller as OrionController;
use Orion\Http\Controllers\RelationController as OrionRelationController;
use ReflectionClass;

/**
 * Injects response schemas and request body schemas for Orion API routes so Scramble
 * documents the correct Resource/CollectionResource responses and Try It request bodies.
 */
class OrionResponseOperationExtension extends OperationExtension
{
    private const REQUEST_NAMESPACE = 'App\\Http\\Requests\\';

    private const RESOURCE_NAMESPACE = 'App\\Http\\Resources\\';

    private const BODY_METHODS = ['post', 'put', 'patch'];

    public function handle(Operation $operation, RouteInfo $routeInfo): void
    {
        $controllerClass = $routeInfo->className();
        if (! $controllerClass || ! $this->isOrionController($controllerClass)) {
            return;
        }

        $modelClass = $this->getControllerModel($controllerClass);
        if (! $modelClass) {
            return;
        }

        $resourceModelClass = $this->resolveResourceModelClass($controllerClass, $modelClass);
        $resourceClass = $this->modelToResourceClass($resourceModelClass);
        $collectionClass = $this->modelToCollectionResourceClass($resourceModelClass);

        if ($resourceClass && class_exists($resourceClass)) {
            $method = $routeInfo->method;
            $isIndex = 'get' === $method && $this->isIndexRoute($routeInfo);
            $isListOperation = $isIndex || ('post' === $method && $this->isBatchRoute($routeInfo));

            if ($isListOperation && class_exists($collectionClass)) {
                $response = $this->openApiTransformer->toResponse(new InferObjectType($collectionClass));
            } else {
                $response = $this->openApiTransformer->toResponse(new InferObjectType($resourceClass));
            }

            if ($response) {
                $this->remove200Responses($operation);
                $operation->addResponse($response);
            }
        }

        $this->injectRequestBodyIfNeeded($operation, $routeInfo, $resourceModelClass ?? $modelClass);
    }

    private function injectRequestBodyIfNeeded(Operation $operation, RouteInfo $routeInfo, string $resourceModelClass): void
    {
        if (! in_array(strtolower($routeInfo->method), self::BODY_METHODS, true)) {
            return;
        }

        if ($this->isSearchRoute($routeInfo)) {
            $operation->addRequestBodyObject(
                RequestBodyObject::make()
                    ->setContent('application/json', Schema::fromType($this->makeSearchRequestBodySchema()))
                    ->required(false)
            );

            return;
        }

        $requestClass = $this->modelToRequestClass($resourceModelClass);
        if (! class_exists($requestClass) || ! is_subclass_of($requestClass, \Orion\Http\Requests\Request::class, true)) {
            return;
        }

        $rules = $this->getOrionRequestRules($requestClass, $routeInfo);
        if ([] === $rules) {
            return;
        }

        $parameters = (new RulesToParameters(
            $rules,
            [],
            $this->openApiTransformer,
            'body'
        ))->mergeDotNotatedKeys(false)->handle();

        if ([] === $parameters) {
            return;
        }

        $schema = Schema::createFromParameters($parameters);
        $operation->addRequestBodyObject(
            RequestBodyObject::make()
                ->setContent('application/json', $schema)
                ->required($this->hasRequiredRules($rules))
        );
    }

    private function getOrionRequestRules(string $requestClass, RouteInfo $routeInfo): array
    {
        $actionMethod = $routeInfo->route->getActionMethod();
        try {
            $request = $requestClass::createFrom(Request::create(
                $routeInfo->route->uri(),
                $routeInfo->method
            ));
            $request->setRouteResolver(fn () => $routeInfo->route);

            return $request->rules();
        } catch (\Throwable) {
            return [];
        }
    }

    private function hasRequiredRules(array $rules): bool
    {
        foreach ($rules as $fieldRules) {
            $a = is_array($fieldRules) ? $fieldRules : [$fieldRules];
            if (in_array('required', $a, true)) {
                return true;
            }
        }

        return false;
    }

    private function isSearchRoute(RouteInfo $routeInfo): bool
    {
        return 'post' === $routeInfo->method && str_contains($routeInfo->route->uri(), '/search');
    }

    private function makeSearchRequestBodySchema(): GeneratorObjectType
    {
        $scopeItem = new GeneratorObjectType;
        $scopeItem->addProperty('name', (new StringType)->setDescription('Scope name'));
        $scopeItem->addProperty('parameters', new ArrayType);

        $filterItem = new GeneratorObjectType;
        $filterItem->addProperty('type', (new StringType)->setDescription('and|or'));
        $filterItem->addProperty('field', (new StringType)->setDescription('Whitelisted field name'));
        $filterItem->addProperty('operator', (new StringType)->setDescription('e.g. =, >=, in, like'));
        $filterItem->addProperty('value', new StringType);

        $searchObj = new GeneratorObjectType;
        $searchObj->addProperty('value', (new StringType)->setDescription('Search phrase'));
        $searchObj->addProperty('case_sensitive', (new StringType)->setDescription('true|false'));

        $sortItem = new GeneratorObjectType;
        $sortItem->addProperty('field', (new StringType)->setDescription('Sortable field'));
        $sortItem->addProperty('direction', (new StringType)->setDescription('asc|desc'));

        $includeItem = new GeneratorObjectType;
        $includeItem->addProperty('relation', new StringType);

        $aggregateItem = new GeneratorObjectType;
        $aggregateItem->addProperty('relation', new StringType);
        $aggregateItem->addProperty('type', (new StringType)->setDescription('count|avg|sum|min|max|exists'));
        $aggregateItem->addProperty('field', new StringType);

        $schema = new GeneratorObjectType;
        $schema->addProperty('scopes', (new ArrayType)->setItems($scopeItem));
        $schema->addProperty('filters', (new ArrayType)->setItems($filterItem));
        $schema->addProperty('search', $searchObj);
        $schema->addProperty('sort', (new ArrayType)->setItems($sortItem));
        $schema->addProperty('includes', (new ArrayType)->setItems($includeItem));
        $schema->addProperty('aggregates', (new ArrayType)->setItems($aggregateItem));

        return $schema;
    }

    private function resolveResourceModelClass(string $controllerClass, string $modelClass): string
    {
        if (! is_subclass_of($controllerClass, OrionRelationController::class, true)) {
            return $modelClass;
        }
        try {
            $ref = new ReflectionClass($controllerClass);
            if (! $ref->hasProperty('relation')) {
                return $modelClass;
            }
            $prop = $ref->getProperty('relation');
            $prop->setAccessible(true);
            $relationName = $prop->getValue($ref->newInstanceWithoutConstructor());
            if (! is_string($relationName)) {
                return $modelClass;
            }
            $parent = app($modelClass);
            $relation = $parent->{$relationName}();

            return get_class($relation->getRelated());
        } catch (\Throwable) {
            return $modelClass;
        }
    }

    private function modelToRequestClass(string $modelClass): string
    {
        $shortName = (new ReflectionClass($modelClass))->getShortName();

        return self::REQUEST_NAMESPACE.$shortName.'Request';
    }

    private function remove200Responses(Operation $operation): void
    {
        if (! $operation->responses) {
            return;
        }
        $operation->responses = array_values(array_filter(
            $operation->responses,
            fn ($r) => 200 !== $this->getResponseCode($r)
        ));
    }

    private function getResponseCode(mixed $response): ?int
    {
        if ($response instanceof Response) {
            return $response->code;
        }
        if ($response instanceof Reference) {
            try {
                return $response->resolve()->code ?? null;
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    private function isOrionController(?string $class): bool
    {
        if (! $class) {
            return false;
        }

        return is_subclass_of($class, OrionController::class, true)
            || is_subclass_of($class, OrionRelationController::class, true);
    }

    private function getControllerModel(string $controllerClass): ?string
    {
        try {
            $ref = new ReflectionClass($controllerClass);
            if (! $ref->hasProperty('model')) {
                return null;
            }
            $prop = $ref->getProperty('model');
            $prop->setAccessible(true);
            $model = $prop->getValue($ref->newInstanceWithoutConstructor());

            return is_string($model) ? $model : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function modelToResourceClass(string $modelClass): string
    {
        $shortName = (new ReflectionClass($modelClass))->getShortName();

        return self::RESOURCE_NAMESPACE.$shortName.'Resource';
    }

    private function modelToCollectionResourceClass(string $modelClass): string
    {
        $shortName = (new ReflectionClass($modelClass))->getShortName();

        return self::RESOURCE_NAMESPACE.$shortName.'CollectionResource';
    }

    private function isIndexRoute(RouteInfo $routeInfo): bool
    {
        if ('get' !== $routeInfo->method) {
            return false;
        }
        $paramNames = $routeInfo->route->parameterNames();
        $controllerClass = $routeInfo->className();
        if (is_subclass_of($controllerClass, OrionRelationController::class, true)) {
            return 1 === count($paramNames);
        }

        return 0 === count($paramNames);
    }

    private function isBatchRoute(RouteInfo $routeInfo): bool
    {
        $path = $routeInfo->route->uri();

        return str_contains($path, 'batch');
    }
}
