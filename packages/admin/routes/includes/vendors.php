<?php

use Illuminate\Support\Facades\Route;
//use Lunar\Hub\Http\Livewire\Pages\Brands\BrandShow;
//use Lunar\Hub\Http\Livewire\Pages\Brands\BrandsIndex;
//use Lunar\Hub\Http\Livewire\Pages\Settings\Staff\VendorCreate;
//use Lunar\Hub\Http\Livewire\Pages\Settings\Staff\VendorsIndex;
//use Lunar\Hub\Http\Livewire\Pages\Settings\Staff\VendorShow;
use Lunar\Hub\Http\Livewire\Pages\Vendors\VendorCreate;
use Lunar\Hub\Http\Livewire\Pages\Vendors\VendorsIndex;
use Lunar\Hub\Http\Livewire\Pages\Vendors\VendorShow;

Route::group([
    'middleware' => 'can:vendors:vendors',
   // 'middleware' => 'can:settings:manage-staff',
], function () {
  //  Route::get('vendors', BrandsIndex::class)->name('hub.brands.index');
   // Route::get('{brand}', BrandShow::class)->name('hub.brands.show');
    Route::get('/', VendorsIndex::class)->name('hub.vendors.index');
    Route::get('create', VendorCreate::class)->name('hub.vendors.create');
    Route::get('{staff}', VendorShow::class)->withTrashed()->name('hub.vendors.show');
});
