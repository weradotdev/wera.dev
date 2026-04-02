<?php

use App\Events\WhatsAppConnectionUpdate;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProjectCommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskUserController;
use App\Http\Controllers\WorkspaceController;
use App\Services\WhatsAppCommandHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Orion\Facades\Orion;

Route::domain(env('API_DOMAIN', 'api.wera.dev'))
    ->as('api.')
    ->group(function () {
        Route::prefix('v1')
            ->as('v1.')
            ->group(function () {
                Route::prefix('auth')
                    ->controller(AuthController::class)
                    ->group(function () {
                        Route::post('login', 'login')->name('login');
                        Route::post('register', 'register')->name('register');

                        Route::prefix('otp')->as('otp.')->group(function () {
                            Route::post('send', 'sendOtp')->name('send');
                            Route::post('verify', 'verifyOtp')->name('verify');
                        });

                        Route::prefix('pin')->as('pin.')->group(function () {
                            Route::post('save', 'savePin')->name('save');
                            Route::post('verify', 'verifyPin')->name('verify');
                        });
                    });

                Route::middleware('auth:sanctum')->group(function () {
                    Route::prefix('auth')
                        ->controller(AuthController::class)
                        ->group(function () {
                            Route::get('me', 'me')->name('me');
                            Route::post('avatar', 'updateAvatar')->name('avatar.update');
                            Route::post('call-token', 'streamToken')->name('stream.token');
                            Route::post('logout', 'logout')->name('logout');

                            // Email verification
                            Route::post('verify-email/{user}', 'sendEmailVerification')->name('verify-email.send');
                            Route::put('verify-email/{user}', 'verifyEmail')->name('verify-email.verify');

                            // Phone verification
                            Route::post('verify-phone/{user}', 'sendPhoneVerification')->name('verify-phone.send');
                            Route::put('verify-phone/{user}', 'verifyPhone')->name('verify-phone.verify');
                        });

                    Route::apiResource('notifications', NotificationController::class);

                    Route::get('stats/{type}', [StatsController::class, 'make'])->name('make');

                    Orion::resource('workspaces', WorkspaceController::class);
                    Orion::resource('projects', ProjectController::class);
                    Orion::resource('boards', BoardController::class);
                    Orion::resource('plans', PlanController::class);
                    Orion::hasManyResource('projects', 'comments', ProjectCommentController::class);

                    Orion::resource('tasks', TaskController::class);
                    Orion::hasManyResource('tasks', 'users', TaskUserController::class);
                });
            });

        Route::post('/whatsapp-incoming', function (Request $request) {
            $token = config('services.whatsapp.callback_token');
            if (filled($token) && $request->header('X-Callback-Token') !== $token) {
                abort(401);
            }
            $sessionId = $request->input('session_id');
            $from = $request->input('from', '');
            $message = $request->input('message', '');
            if (blank($sessionId) || blank($from)) {
                abort(422, 'session_id and from required');
            }
            $reply = app(WhatsAppCommandHandler::class)->handle($sessionId, $from, $message);

            return response()->json(['reply' => $reply]);
        })->name('whatsapp.incoming');

        Route::post('/whatsapp-callback', function (Request $request) {
            $token = config('services.whatsapp.callback_token');
            if (filled($token) && $request->header('X-Callback-Token') !== $token) {
                abort(401);
            }
            $sessionId = $request->input('session_id');
            if (blank($sessionId)) {
                abort(422, 'session_id required');
            }
            $qr = $request->input('qr');
            $connected = (bool) $request->input('connected', false);
            event(new WhatsAppConnectionUpdate($sessionId, $qr, $connected));

            return response()->json(['ok' => true]);
        })->name('whatsapp.callback');
    });
