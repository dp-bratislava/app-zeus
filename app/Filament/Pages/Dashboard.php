<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Page;
use Filament\Resources\Resource;

class Dashboard extends BaseDashboard
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getLinks(): array
    {
        return collect(config('admin-panel.navigation.items', []))
            ->filter(fn (string $class) => $this->canAccessItem($class))
            ->map(fn (string $class) => [
                'label' => $class::getNavigationLabel(),
                'icon' => $class::getNavigationIcon(),
                'url' => $this->urlFor($class),
            ])
            ->values()
            ->all();
    }

    private function canAccessItem(string $class): bool
    {
        if (is_subclass_of($class, Resource::class)) {
            return $class::canViewAny();
        }

        if (is_subclass_of($class, Page::class)) {
            return $class::canAccess();
        }

        return true;
    }

    private function urlFor(string $class): string
    {
        if (is_subclass_of($class, Resource::class)) {
            return $class::getUrl('index');
        }

        return $class::getUrl();
    }
}

