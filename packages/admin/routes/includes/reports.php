<?php

use Illuminate\Support\Facades\Route;
use Lunar\Hub\Http\Livewire\Components\Reports\ReportsIndex;
use Lunar\Hub\Http\Livewire\Components\Reports\ReportsOrderLine;

Route::group([
    'middleware' => 'can:reports:reports',
   // 'middleware' => 'can:settings:manage-staff',
], function () {
    Route::get('/', ReportsIndex::class)->name('hub.reports.index');
    Route::get('/order-line/{brand_id}/{filter}', ReportsOrderLine::class)->name('hub.reports.order-line');
   // Route::get('{id}', ReportShow::class)->withTrashed()->name('hub.reports.show');
});

//'middleware' => 'can:vendors:vendors',