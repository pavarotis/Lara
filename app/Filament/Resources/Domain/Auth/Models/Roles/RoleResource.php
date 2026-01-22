<?php

namespace App\Filament\Resources\Domain\Auth\Models\Roles;

use App\Domain\Auth\Models\Role;
use App\Filament\Resources\Domain\Auth\Models\Roles\Pages\CreateRole;
use App\Filament\Resources\Domain\Auth\Models\Roles\Pages\EditRole;
use App\Filament\Resources\Domain\Auth\Models\Roles\Pages\ListRoles;
use App\Filament\Resources\Domain\Auth\Models\Roles\Schemas\RoleForm;
use App\Filament\Resources\Domain\Auth\Models\Roles\Tables\RolesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return RoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RolesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
