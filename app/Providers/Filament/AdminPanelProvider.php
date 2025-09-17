<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => '<link rel="stylesheet" href="' . asset('css/filament-dashboard.css') . '">',
        );
    }
    
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Bakery Shop')
            ->darkMode(true)
            ->sidebarCollapsibleOnDesktop()
            ->topNavigation(false)
            ->navigationGroups([
                'Dashboard',
                'Order Management',
                'Payments & Shipments',
                'Shop Management',
                'User Management',
                'Pages',
                'Reports',
                'Campaigns',
                'System Settings',
            ])
            ->resources([
                \App\Filament\Resources\ProductResource::class,
                \App\Filament\Resources\CategoryResource::class,
                \App\Filament\Resources\OrderResource::class,
                \App\Filament\Resources\AdminResource::class,
                \App\Filament\Resources\CustomerResource::class,
                \App\Filament\Resources\AuditLogResource::class,
                \App\Filament\Resources\RoleResource::class,
                \App\Filament\Resources\UserResource::class,
                \App\Filament\Resources\PaymentResource::class,
                \App\Filament\Resources\ShipmentResource::class,
                \App\Filament\Resources\CouponResource::class,
                \App\Filament\Resources\ComboOfferResource::class,
                \App\Filament\Resources\LimitedTimeOfferResource::class,
                \App\Filament\Resources\HeroSlideResource::class,
                \App\Filament\Resources\HomePageCategoryResource::class,
                \App\Filament\Resources\PopularProductResource::class,
                \App\Filament\Resources\TestimonialResource::class,
                \App\Filament\Resources\HomepageFaqResource::class,
                \App\Filament\Resources\WhyChooseUsResource::class,
                \App\Filament\Resources\BlogResource::class,
                \App\Filament\Resources\MessageCampaignResource::class,
                \App\Filament\Resources\HeaderFooterSettingsResource::class,
                \App\Filament\Resources\InvoiceResource::class,
                \App\Filament\Resources\BundleResource::class,
                \App\Filament\Resources\ProductVariantResource::class,
                \App\Filament\Resources\AboutUsResource::class,
                \App\Filament\Resources\ContactUsResource::class,
            ])
            ->pages([
                \App\Filament\Pages\Dashboard::class,
                \App\Filament\Pages\HomePage::class,
                \App\Filament\Pages\AboutPage::class,
                \App\Filament\Pages\ContactPage::class,
                \App\Filament\Pages\HeaderFooterManagement::class,
                \App\Filament\Pages\AdminSettings::class,
                \App\Filament\Pages\Profile::class,
                \App\Filament\Pages\ReportsAndExports::class,
            ])
            ->widgets([
                // Dashboard widgets are registered in Dashboard page
            ])
            ->breadcrumbs(true)
            ->authGuard('web')
            
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
