<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class VqmodManager extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.vqmod-manager';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationLabel = 'Vqmod Manager';

    public function getTitle(): string
    {
        return 'Vqmod Manager';
    }
}
