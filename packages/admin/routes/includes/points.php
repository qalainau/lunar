<?php

use Illuminate\Support\Facades\Route;
use Lunar\Hub\Http\Livewire\Components\Points\PointsIndex;
use Lunar\Hub\Http\Livewire\Components\Points\PointsOrderLine;

Route::group([
    'middleware' => 'can:points:points',
   // 'middleware' => 'can:settings:manage-staff',
], function () {
    Route::get('/', PointsIndex::class)->name('hub.points.index');
    Route::get('/order-line/{brand_id}/{filter}', PointsOrderLine::class)->name('hub.points.order-line');
   // Route::get('{id}', ReportShow::class)->withTrashed()->name('hub.reports.show');
});

//'middleware' => 'can:vendors:vendors',