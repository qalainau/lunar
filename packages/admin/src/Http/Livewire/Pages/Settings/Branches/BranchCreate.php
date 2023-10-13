<?php

namespace Lunar\Hub\Http\Livewire\Pages\Settings\Branches;

use Livewire\Component;

class BranchCreate extends Component
{
    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.pages.settings.branches.create')
            ->layout('adminhub::layouts.settings', [
                'menu' => 'settings',
            ]);
    }
}
