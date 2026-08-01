<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\SiteSettings;
use App\Filament\Pages\ZoneCamps;
use App\Models\Zone;
use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
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
            ->brandName(fn () => SiteSettings::currentTitle())
            ->brandLogo(asset('images/yl-logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('images/yl-logo.png'))
            // brandName above only becomes the logo's alt text once a
            // brandLogo is set (see vendor/filament/.../logo.blade.php), so
            // it's not actually visible — render it as its own banner in the
            // topbar instead, spanning the full width above the page.
            ->renderHook(
                \Filament\View\PanelsRenderHook::TOPBAR_START,
                fn () => view('filament.partials.site-title-banner'),
            )
            ->login(Login::class)
            ->colors([
                'primary' => Color::hex('#106165'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationItems($this->zoneNavigationItems())
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * One sidebar item per zone (Métro l'Ouest, Métro Nord-Ouest, ...),
     * matching the client's legacy dashboard. Built fresh from the zones
     * table on every request — a new zone shows up with no code change —
     * and each item's visibility is a lazily-evaluated closure (not resolved
     * here) because auth isn't available yet this early in the request
     * lifecycle.
     *
     * @return array<NavigationItem>
     */
    protected function zoneNavigationItems(): array
    {
        // Guards against the panel booting before migrations have run (e.g.
        // a fresh test database) rather than hard-crashing every request.
        if (! \Illuminate\Support\Facades\Schema::hasTable('zones')) {
            return [];
        }

        return Zone::query()
            ->orderBy('id')
            ->get()
            ->map(fn (Zone $zone) => NavigationItem::make($zone->name)
                ->icon('heroicon-o-map')
                ->sort($zone->id)
                ->url(fn () => ZoneCamps::getUrl(['zone' => $zone->id]))
                ->isActiveWhen(fn () => request()->route('zone')?->is($zone))
                ->visible(fn () => auth()->check() && (
                    auth()->user()->isSuperAdmin()
                    || auth()->user()->zones()->whereKey($zone->id)->exists()
                )))
            ->all();
    }
}
