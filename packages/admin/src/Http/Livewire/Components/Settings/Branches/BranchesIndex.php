<?php

namespace Lunar\Hub\Http\Livewire\Components\Settings\Branches;

use Livewire\Component;

class BranchesIndex extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.settings.branches.index')
            ->layout('adminhub::layouts.base');
    }
}
