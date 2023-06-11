<?php

use Illuminate\Support\Facades\Route;
use Lunar\Hub\Http\Livewire\Components\Points\PointsIndex;
use Lunar\Hub\Http\Livewire\Components\Points\PointsOrderLine;

Route::group([
    'middleware' => 'can:points:points',
   // 'middleware' => 'can:settings:manage-staff',
], function () {
    Route::get('/', PointsIndex::class)->name('hub.points.index');
});

//'middleware' => 'can:vendors:vendors',