<?php

namespace Lunar\Hub\Http\Livewire\Pages\Settings\Branches;

use Livewire\Component;
use Lunar\Hub\Models\Branch;

class BranchShow extends Component
{
    public Branch $branch;

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.pages.settings.branches.show')
            ->layout('adminhub::layouts.settings', [
                'menu' => 'settings',
            ]);
    }
}
