<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return false; // We override the default dashboard, so no sidebar item
    }    
}
