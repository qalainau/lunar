<?php

namespace Lunar\Hub\Http\Livewire\Components\Settings\Branches;

use Illuminate\Support\Facades\Hash;
use Lunar\Hub\Auth\Manifest;
use Lunar\Hub\Models\Branch;

class BranchCreate extends AbstractBranches
{
    /**
     * Called when the component has been mounted.
     *
     * @return void
     */
    public function mount()
    {
        $this->branch = new Branch();
    }

    /**
     * Define the validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [

            'branch.name' => 'string|max:255',

        ];
    }

    /**
     * Create the staff member.
     *
     * @return void
     */
    public function create()
    {
        $this->validate();
        $this->branch->save();
        $this->notify('エリア名を追加しました.', 'hub.branches.index');
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render(Manifest $manifest)
    {
        // dd($this);
        $permissions = $manifest->getGroupedPermissions();

        return view('adminhub::livewire.components.settings.branches.create', [
            'firstPartyPermissions' => $permissions->filter(fn($permission) => (bool)$permission->firstParty),
        ])->layout('adminhub::layouts.base');
    }
}
