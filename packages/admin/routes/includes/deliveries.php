<?php

use Illuminate\Support\Facades\Route;
use Lunar\Hub\Http\Livewire\Components\Deliveries\DeliveriesIndex;
use Lunar\Hub\Http\Livewire\Components\Deliveries\DeliveriesOrderLine;

Route::group([
    'middleware' => 'can:deliveries:deliveries',
    // 'middleware' => 'can:settings:manage-staff',
], function () {
    Route::get('/', DeliveriesIndex::class)->name('hub.deliveries.index');
    Route::get('/order-line/{brand_id}/{filter}', DeliveriesOrderLine::class)->name('hub.deliveries.order-line');
    // Route::get('{id}', ReportShow::class)->withTrashed()->name('hub.reports.show');
});

//'middleware' => 'can:vendors:vendors',