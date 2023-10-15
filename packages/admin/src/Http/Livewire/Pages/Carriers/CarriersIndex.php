<?php

namespace Lunar\Hub\Http\Livewire\Pages\Carriers;

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
        return view('adminhub::livewire.pages.carriers.index')
            ->layout('adminhub::layouts.app', [
                'title' => 'ベンダー一覧',
            ]);

    }
}
