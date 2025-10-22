<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class FleetPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('fleet')
            ->path('fleet')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->topNavigation()
            ->discoverResources(in: app_path('Filament/Fleet/Resources'), for: 'App\\Filament\\Fleet\\Resources')
            ->discoverPages(in: app_path('Filament/Fleet/Pages'), for: 'App\\Filament\\Fleet\\Pages')
            ->pages([
                // Pages\Dashboard::class,
            ])
            ->navigationItems([
                NavigationItem::make('tickets')
                    ->label('tickets')
                    // ->url(fn (): string => Dashboard::getUrl())
                    ->url(fn (): string => route('filament.admin.resources.ticket.tickets.index'))
            ])
            ->discoverWidgets(in: app_path('Filament/Fleet/Widgets'), for: 'App\\Filament\\Fleet\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
