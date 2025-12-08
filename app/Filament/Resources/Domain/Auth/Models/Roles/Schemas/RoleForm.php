<?php

namespace App\Filament\Resources\Domain\Auth\Models\Roles\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('URL-friendly identifier (e.g., admin, editor)'),
                Textarea::make('description')
                    ->rows(3)
                    ->maxLength(500),
                Toggle::make('is_system')
                    ->label('System Role')
                    ->helperText('System roles cannot be deleted')
                    ->default(false)
                    ->disabled(fn ($record) => $record?->is_system ?? false),
                Select::make('permissions')
                    ->label('Permissions')
                    ->relationship('permissions', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->helperText('Select permissions for this role'),
            ]);
    }
}
