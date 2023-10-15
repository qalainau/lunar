<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Lunar\Hub\Http\Livewire\Hub;
use Lunar\Hub\Http\Livewire\Pages\Account;
use Lunar\Hub\Http\Livewire\Pages\Authentication\Login;
use Lunar\Hub\Http\Livewire\Pages\Authentication\PasswordReset;
use Lunar\Hub\Http\Middleware\Authenticate;
use Lunar\Hub\Http\Middleware\RedirectIfAuthenticated;

Route::group([
    'prefix' => config('lunar-hub.system.path', 'hub'),
    'middleware' => ['web'],
], function () {
    Route::post('logout', function () {
        Auth::guard('staff')->logout();

        return redirect()->route('hub.login');
    })->name('hub.logout');

    Route::group([
        'middleware' => RedirectIfAuthenticated::class,
    ], function ($router) {
        $router->get('login', Login::class)->name('hub.login');
        $router->get('password-reset', PasswordReset::class)->name('hub.password-reset');
    });

    Route::group([
        'middleware' => [
            Authenticate::class,
        ],
    ], function ($router) {
        $router->get('/', Hub::class)->name('hub.index');

        Route::get('account', Account::class)->name('hub.account');

        Route::group([
            'prefix' => 'products',
        ], __DIR__ . '/includes/products.php');

        Route::group([
            'prefix' => 'product-types',
        ], __DIR__ . '/includes/product-types.php');

        Route::group([
            'prefix' => 'orders',
        ], __DIR__ . '/includes/orders.php');

        Route::group([], __DIR__ . '/includes/collections.php');

        Route::group([
            'prefix' => 'settings',
        ], __DIR__ . '/includes/settings.php');

        Route::group([
            'prefix' => 'customers',
        ], __DIR__ . '/includes/customers.php');

        Route::group([
            'prefix' => 'discounts',
        ], __DIR__ . '/includes/discounts.php');

        Route::group([
            'prefix' => 'brands',
        ], __DIR__ . '/includes/brands.php');
        Route::group([
            'prefix' => 'vendors',
        ], __DIR__ . '/includes/vendors.php');
        Route::group([
            'prefix' => 'carriers',
        ], __DIR__ . '/includes/carriers.php');
        Route::group([
            'prefix' => 'deliveries',
        ], __DIR__ . '/includes/deliveries.php');

        Route::group([
            'prefix' => 'reports',
        ], __DIR__ . '/includes/reports.php');

        Route::group([
            'prefix' => 'points',
        ], __DIR__ . '/includes/points.php');

        Route::group([
            'prefix' => 'vendor-settings',
        ], __DIR__ . '/includes/vendor-settings.php');

        Route::group([
            'prefix' => 'assets',
        ], __DIR__ . '/includes/assets.php');
    });
});
