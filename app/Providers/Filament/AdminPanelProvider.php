<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Tenancy\EditBranchProfile;
use App\Filament\Pages\Tenancy\RegisterBranch;
use App\Filament\Resources\PurchaseResource\Pages\CreatePurchase;
use App\Models\Branch;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login()
            ->passwordReset()
            ->profile(EditProfile::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->tenant(
                Branch::class,
                ownershipRelationship: 'branch',
                slugAttribute: 'slug'
            )
            ->tenantRegistration(RegisterBranch::class)
            ->tenantProfile(EditBranchProfile::class)
            ->tenantRoutePrefix('branch')
            ->tenantMenuItems([
                'profile' => MenuItem::make()->label('Update branch details'),
                'register' => MenuItem::make()->label('Add new branch'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Store')
                    ->icon('heroicon-o-shopping-cart')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->navigationItems([
                NavigationItem::make('create_purchase')
                    ->url(fn (): string => CreatePurchase::getUrl())
                    ->icon('heroicon-o-shopping-bag')
                    ->sort(1)
                    ->visible(fn (): bool => auth()->user()->can('create_purchase'))
                    ->label(fn (): string => 'Create Purchase')
                    ->isActiveWhen(fn () => request()->routeIs(CreatePurchase::getRouteName())),
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()->label('Edit profile'),
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
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->spa();
    }
}
