<?php

namespace Lunar\Hub\Http\Livewire\Pages\Carriers;

use Livewire\Component;

class CarrierCreate extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.pages.carriers.create')
            ->layout('adminhub::layouts.app', [
                'title' => 'ベンダー追加',
            ]);
    }
}
