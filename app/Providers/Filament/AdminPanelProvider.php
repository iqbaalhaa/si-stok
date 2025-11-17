<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use App\Filament\Widgets\StockMovementChart;
use App\Filament\Widgets\MotorStockPieChart;
use App\Filament\Widgets\InventoryStatsOverview;
use Filament\Notifications\Notification;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Resources\MotorResource;
use App\Filament\Resources\StokMasukResource;
use App\Filament\Resources\StokKeluarResource;
use App\Filament\Resources\HargaMotorResource;
use App\Filament\Resources\StokOpnameResource;
use App\Filament\Pages\Profile;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName(fn () => SystemSetting::query()->value('nama_sistem') ?? config('app.name'))
            ->brandLogo(fn () => ($logo = SystemSetting::query()->value('logo')) ? asset(Storage::url($logo)) : null)
            ->brandLogoHeight('6rem')
            ->darkModeBrandLogo(fn () => ($logo = SystemSetting::query()->value('logo')) ? asset(Storage::url($logo)) : null)
            ->favicon(fn () => ($fav = SystemSetting::query()->value('favicon')) ? asset(Storage::url($fav)) : null)
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, function () {
                $customUrl = env('FILAMENT_LOGIN_LOGO_URL');
                $loginLogo = $customUrl ?: SystemSetting::query()->value('login_logo');
                $src = $loginLogo ? (str_starts_with($loginLogo, 'http') ? $loginLogo : asset(Storage::url($loginLogo))) : null;
                if (! $src) {
                    $fallback = SystemSetting::query()->value('logo');
                    $src = $fallback ? asset(Storage::url($fallback)) : null;
                }
                if (! $src) {
                    return '';
                }
                return new HtmlString('<style>.fi-logo{display:none !important}.fi-simple-header-heading{display:none !important}</style><div class="mt-2 mb-6 flex flex-col items-center gap-2"><img src="' . e($src) . '" alt="Login Logo" class="h-16"><h1 class="text-center text-2xl font-bold tracking-tight text-gray-950 dark:text-white">LOGIN</h1></div>');
            })
            ->renderHook(PanelsRenderHook::FOOTER, function () {
                $text = SystemSetting::query()->value('footer_text');
                if (! $text) {
                    return '';
                }
                return new HtmlString('<div class="mt-8 w-full border-t border-gray-200 dark:border-gray-800 py-4 text-center text-sm text-gray-600 dark:text-gray-400">' . e($text) . '</div>');
            })
            ->colors([
                'primary' => '#e30613',
                'warning' => '#e30613',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                InventoryStatsOverview::class,
                StockMovementChart::class,
                MotorStockPieChart::class,
            ])
            ->navigationGroups([
                'Data Master',
                'Manajemen Stok',
                'Pengaturan',
            ])
            ->navigationItems([
                NavigationItem::make('Laporan Stok')
                    ->group('Laporan')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->url(fn (): string => MotorResource::getUrl('index'))
                    ->visible(fn (): bool => auth()->user()?->role === 'kepala'),
                NavigationItem::make('Laporan Stok Masuk')
                    ->group('Laporan')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (): string => StokMasukResource::getUrl('index'))
                    ->visible(fn (): bool => auth()->user()?->role === 'kepala'),
                NavigationItem::make('Laporan Stok Keluar')
                    ->group('Laporan')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->url(fn (): string => StokKeluarResource::getUrl('index'))
                    ->visible(fn (): bool => auth()->user()?->role === 'kepala'),
                NavigationItem::make('Laporan Harga Motor')
                    ->group('Laporan')
                    ->icon('heroicon-o-currency-dollar')
                    ->url(fn (): string => HargaMotorResource::getUrl('index'))
                    ->visible(fn (): bool => auth()->user()?->role === 'kepala'),
                NavigationItem::make('Laporan Stok Opname')
                    ->group('Laporan')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->url(fn (): string => StokOpnameResource::getUrl('index'))
                    ->visible(fn (): bool => auth()->user()?->role === 'kepala'),
            ])
            ->bootUsing(function () {
                if (! auth()->check()) {
                    return;
                }

                if (session()->get('low_stock_notified')) {
                    return;
                }

                $threshold = 3;
                $count = \App\Models\Motor::query()->where('stok', '<=', $threshold)->count();
                if ($count > 0) {
                    Notification::make()
                        ->title('Stok menipis')
                        ->body('Ada ' . $count . ' motor dengan stok ≤ ' . $threshold)
                        ->warning()
                        ->persistent()
                        ->send();

                    session()->put('low_stock_notified', true);
                }
            })
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
