<?php

namespace Lunar\Hub\Http\Livewire\Components\Vendors;

use Livewire\Component;

class VendorsIndex extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.vendors.index')
            ->layout('adminhub::layouts.base');
    }
}
