<?php

namespace Lunar\Hub\Http\Livewire\Pages\Vendors;

use Livewire\Component;

class VendorCreate extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.pages.vendors.create')
            ->layout('adminhub::layouts.app', [
                'title' => 'ベンダー追加',
            ]);
    }
}
