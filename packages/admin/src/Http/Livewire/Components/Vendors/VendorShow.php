<?php

namespace Lunar\Hub\Http\Livewire\Components\Vendors;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Lunar\Hub\Auth\Manifest;
use Lunar\Hub\Http\Livewire\Traits\ConfirmsDelete;
use Lunar\Hub\Models\Staff;

class VendorShow extends AbstractStaff
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
        $this->branch_id = $this->staff->brand->branch_id;
        $this->carrier_id = $this->staff->brand->carrier_id;
        $this->plan_id = $this->staff->brand->plan_id;
    }

    /**
     * Define the validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        $staff_email = $this->staff->email;
        ray($staff_email);
        return [
            'staffPermissions' => 'array',
            'staff.email' => 'required|email|unique:' . get_class($this->staff) . ',email,' . $this->staff->id . ',id,deleted_at,NULL',
//            'staff.email' => ['required', 'email', Rule::unique('lunar_staff', 'email')->where(static function ($query) use ($staff_email) {
//
//                return $query->whereNull('deleted_at')->where('email', '!=', $staff_email);
//            })],
            'staff.firstname' => 'string|max:255',
            'staff.lastname' => 'string|max:255',
            'staff.company_name' => 'string|max:255|nullable',
            'staff.member_name' => 'string|max:255|nullable',
            'staff.phone_number' => 'string|max:255',
            'staff.address' => 'string|max:255',
            'staff.post_code' => 'string|max:255',
            'staff.admin' => 'nullable|boolean',
            'branch_id' => 'required|integer',
            'carrier_id' => 'required|integer',
            'password' => 'nullable|min:8|max:255|confirmed',
            'plan_id' => 'required|integer',
        ];
    }

    /**
     * Delete a staff member.
     *
     * @return void
     */
    public function delete()
    {

        //商品も削除
        $this->staff->brand->products()->delete();
        //店舗情報も削除する
        $this->staff->brand->delete();
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
        ray($this->staff->brand->name);

        $this->staff->save();
        $this->staff->brand->name = $this->staff->lastname;
        $this->staff->brand->branch_id = $this->branch_id;
        $this->staff->brand->carrier_id = $this->carrier_id;
        $this->staff->brand->plan_id = $this->plan_id;
        $this->staff->brand->save();

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

        return view('adminhub::livewire.components.vendors.show', [
            'firstPartyPermissions' => $permissions->filter(fn($permission) => (bool)$permission->firstParty),
        ])->layout('adminhub::layouts.base');
    }
}
