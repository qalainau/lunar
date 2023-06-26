<?php

use Illuminate\Support\Facades\Route;
use Lunar\Hub\Http\Livewire\Components\VendorSettings\VendorSettingsIndex;


Route::group([
    'middleware' => 'can:vendor-settings:vendor-settings',
], function () {
    Route::get('/', VendorSettingsIndex::class)->name('hub.vendor-settings.index');

});

