<?php

namespace Lunar\Hub\Http\Livewire\Components\Carriers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Lunar\Hub\Auth\Manifest;
use Lunar\Hub\Http\Livewire\Traits\ConfirmsDelete;
use Lunar\Hub\Models\Staff;

class CarrierShow extends AbstractStaff
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
        $this->staffPermissions = $this->staff->permissions->pluck('handle');
    }

    /**
     * Define the validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'staffPermissions' => 'array',
            'staff.email' => 'required|email|unique:' . get_class($this->staff) . ',email,' . $this->staff->id,
            'staff.firstname' => 'string|max:255',
            'staff.lastname' => 'string|max:255',
            'staff.phone_number' => 'string|max:255',
            'staff.address' => 'string|max:255',
            'staff.post_code' => 'string|max:255',
            'staff.admin' => 'nullable|boolean',
            'password' => 'nullable|min:8|max:255|confirmed',
        ];
    }

    /**
     * Delete a staff member.
     *
     * @return void
     */
    public function delete()
    {
        $this->staff->delete();
        $this->notify('Staff member was removed', 'hub.staff.index');
    }

    /**
     * Restore the product.
     *
     * @return void
     */
    public function restore()
    {
        $this->staff->restore();
        $this->showRestoreConfirm = false;
        $this->notify(
            __('adminhub::notifications.staff.restored')
        );
    }

    /**
     * Returns whether we have met the criteria to allow deletion.
     *
     * @return bool
     */
    public function getCanDeleteProperty()
    {
        return $this->deleteConfirm === $this->staff->email;
    }

    /**
     * Computed property to determine if we're editing ourself.
     *
     * @return bool
     */
    public function getOwnAccountProperty()
    {
        return $this->staff->id == Auth::user()->id;
    }

    /**
     * Update the staff member.
     *
     * @return void
     */
    public function update()
    {
        $this->validate();

        // If we only have one admin, we can't remove it.
        if (!$this->staff->admin && !Staff::where('id', '!=', $this->staff->id)->whereAdmin(true)->exists()) {
            $this->notify('You must have at least one admin');

            return;
        }

        if ($this->password) {
            $this->staff->password = Hash::make($this->password);
        }


        $this->staff->save();


        //  $this->syncPermissions();

        $this->notify('ベンダーを更新しました');
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

        return view('adminhub::livewire.components.carriers.show', [
            'firstPartyPermissions' => $permissions->filter(fn($permission) => (bool)$permission->firstParty),
        ])->layout('adminhub::layouts.base');
    }
}
