<?php

namespace Lunar\Hub\Http\Livewire\Pages\Vendors;

use Livewire\Component;
use Lunar\Hub\Models\Staff;

class VendorShow extends Component
{
    public Staff $staff;

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.pages.vendors.show')
            ->layout('adminhub::layouts.app', [
                'title' => 'ベンダー管理・編集',
            ]);
    }
}
