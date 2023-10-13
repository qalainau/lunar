<?php

namespace Lunar\Hub\Http\Livewire\Components\Settings\Branches;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Lunar\Hub\Auth\Manifest;
use Lunar\Hub\Http\Livewire\Traits\ConfirmsDelete;
use Lunar\Hub\Models\Branch;

class BranchShow extends AbstractBranches
{
    use ConfirmsDelete;

    /**
     * Whether to show the delete confirmation modal.
     *
     * @var bool
     */
    public $showRestoreConfirm = false;

    /**
     * Whether to show the delete confirmation modal.
     *
     * @var bool
     */
    public $showDeleteConfirm = false;

    /**
     * Called when the component has been mounted.
     *
     * @return void
     */
    public function mount()
    {

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
     * Delete a branch member.
     *
     * @return void
     */
    public function delete()
    {
        $this->branch->delete();
        $this->notify('branch member was removed', 'hub.branch.index');
    }

    /**
     * Restore the product.
     *
     * @return void
     */
    public function restore()
    {
        $this->branch->restore();
        $this->showRestoreConfirm = false;
        $this->notify(
            __('adminhub::notifications.branch.restored')
        );
    }

    /**
     * Returns whether we have met the criteria to allow deletion.
     *
     * @return bool
     */
    public function getCanDeleteProperty()
    {
        return $this->deleteConfirm === $this->branch->name;
    }

    /**
     * Computed property to determine if we're editing ourself.
     *
     * @return bool
     */
    public function getOwnAccountProperty()
    {
        return $this->branch->id == Auth::user()->id;
    }

    /**
     * Update the branch member.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();

        $this->branch->save();


        $this->notify('branch member updated');
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

        return view('adminhub::livewire.components.settings.branches.show', [
            'firstPartyPermissions' => $permissions->filter(fn($permission) => (bool)$permission->firstParty),
        ])->layout('adminhub::layouts.base');
    }
}
