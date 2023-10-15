<?php

use Illuminate\Support\Facades\Route;

//use Lunar\Hub\Http\Livewire\Pages\Brands\BrandShow;
//use Lunar\Hub\Http\Livewire\Pages\Brands\BrandsIndex;
//use Lunar\Hub\Http\Livewire\Pages\Settings\Staff\VendorCreate;
//use Lunar\Hub\Http\Livewire\Pages\Settings\Staff\VendorsIndex;
//use Lunar\Hub\Http\Livewire\Pages\Settings\Staff\VendorShow;
use Lunar\Hub\Http\Livewire\Pages\Carriers\CarrierCreate;
use Lunar\Hub\Http\Livewire\Pages\Carriers\CarriersIndex;
use Lunar\Hub\Http\Livewire\Pages\Carriers\CarrierShow;

Route::group([
    'middleware' => 'can:carriers:carriers',
    // 'middleware' => 'can:settings:manage-staff',
], function () {
    //  Route::get('vendors', BrandsIndex::class)->name('hub.brands.index');
    // Route::get('{brand}', BrandShow::class)->name('hub.brands.show');
    Route::get('/', CarriersIndex::class)->name('hub.carriers.index');
    Route::get('create', CarrierCreate::class)->name('hub.carriers.create');
    Route::get('{staff}', CarrierShow::class)->withTrashed()->name('hub.carriers.show');
});
