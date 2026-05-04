<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Register extends \Filament\Auth\Pages\Register
{
    

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        $user=  $this->getUserModel()::create([...$data, 'type' => 'project_manager']);

        $name = "My Workspace";
        
        $user->workspaces()->create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        return $user;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPhoneFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function getNameFormComponent(): Component
    {
        return Grid::make(2)
        ->schema([TextInput::make('first_name')
            ->label(__('auth.first_name'))
            ->required()
            ->maxLength(255)
            ->autofocus(),TextInput::make('last_name')
            ->label(__('auth.last_name'))
            ->required()
            ->maxLength(255)]);
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label(__('auth.phone'))
            ->tel()
            ->required()
            ->maxLength(255);
    }

}