<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Navigation\NavigationGroup;
use Filament\Support\Colors\Color;
use Filament\Enums\ThemeMode;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            // Identité Antika : cuivre du site, neutres chauds, logo, thème clair par défaut.
            ->brandName('Antika')
            ->brandLogo(asset('images/admin/logo-dark.png'))
            ->darkModeBrandLogo(asset('images/admin/logo-light.png'))
            ->brandLogoHeight('2.6rem')
            ->favicon(asset('favicon-32.png'))
            ->font('Figtree')
            ->defaultThemeMode(ThemeMode::Light)
            ->colors([
                'primary' => Color::hex('#d9551f'),
                'gray' => Color::Stone,
            ])
            ->sidebarWidth('17rem')
            // Styles complémentaires (fonds, cartes, menu, mobile) : resources/views/filament/admin-theme.blade.php
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('filament.admin-theme'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            // Groupes repliés à l'ouverture : on déplie celui dont on a besoin.
            ->navigationGroups([
                NavigationGroup::make('Location de salle')->collapsed(),
                NavigationGroup::make('Carte')->collapsed(),
                NavigationGroup::make('Soirée événement')->collapsed(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                // Admin toujours en français (le site public, lui, suit la langue du visiteur).
                \App\Http\Middleware\AdminLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
