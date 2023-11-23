<?php

use Illuminate\Support\Facades\Route;
use Lunar\Hub\Http\Livewire\Components\Sliders\SlidersIndex;
use Lunar\Hub\Http\Livewire\Components\Sliders\SliderEdit;


//use Lunar\Hub\Http\Livewire\Components\Sliders\SlidersOrderLine;

Route::group([
    'middleware' => 'can:sliders:sliders',
    // 'middleware' => 'can:settings:manage-staff',
], function () {
    Route::get('/', SlidersIndex::class)->name('hub.sliders.index');

    Route::get('/edit/{id}', SliderEdit::class)->name('hub.sliders.edit');
    //  Route::get('/order-line/{brand_id}/{filter}', ReportsOrderLine::class)->name('hub.reports.order-line');
    // Route::get('{id}', ReportShow::class)->withTrashed()->name('hub.reports.show');
});

//'middleware' => 'can:vendors:vendors',