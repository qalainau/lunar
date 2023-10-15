<?php

namespace Lunar\Hub\Http\Livewire\Components\Carriers;

use Livewire\Component;

class CarriersIndex extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.carriers.index')
            ->layout('adminhub::layouts.base');
    }
}
