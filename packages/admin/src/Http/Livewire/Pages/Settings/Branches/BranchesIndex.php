<?php

namespace Lunar\Hub\Http\Livewire\Pages\Settings\Branches;

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
        return view('adminhub::livewire.pages.settings.branches.index')
            ->layout('adminhub::layouts.settings', [
                'menu' => 'settings',
            ]);
    }
}
