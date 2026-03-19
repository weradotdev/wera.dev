<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                PhoneInput::make('phone')
                    ->required()
                    ->defaultCountry('KE')
                    ->unique(ignoreRecord: true),
                Select::make('type')
                    ->required()
                    ->default('developer')
                    ->options([
                        'admin'           => 'Admin',
                        'developer'       => 'Developer',
                        'client'          => 'Client',
                        'project_manager' => 'Project Manager',
                    ]),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => 'create' === $context)
                    ->autocomplete(false),
            ]);
    }
}
