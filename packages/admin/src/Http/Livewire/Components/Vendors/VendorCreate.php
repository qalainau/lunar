<?php

namespace Lunar\Hub\Http\Livewire\Components\Vendors;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Lunar\Hub\Auth\Manifest;
use Lunar\Hub\Models\Staff;

class VendorCreate extends AbstractStaff
{
    /**
     * Called when the component has been mounted.
     *
     * @return void
     */
    public function mount()
    {
        $this->staff = new Staff();
        $this->staffPermissions = $this->staff->permissions->pluck('handle');
        $this->carrier_id = 0;
        $this->plan_id = null;
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
            //  'staff.email' => 'required|email|unique:' . get_class($this->staff) . ',email,deleted_at,NULL',
            'staff.email' => ['required', 'email', Rule::unique('lunar_staff', 'email')->where(static function ($query) {
                return $query->whereNull('deleted_at');
            })],
            'staff.firstname' => 'string|max:255',
            'staff.company_name' => 'string|max:255|nullable',
            'staff.member_name' => 'string|max:255|nullable',
            'staff.lastname' => 'required|string|max:255',
            'staff.phone_number' => 'string|max:255',
            'staff.address' => 'string|max:255',
            'staff.post_code' => 'string|max:255',
            'staff.admin' => 'nullable|boolean',
            'password' => 'required|min:8|max:255|confirmed',
            'password_confirmation' => 'string',
            'branch_id' => 'required|integer',
            'carrier_id' => 'required|integer',
            'plan_id' => 'required|integer',
        ];
    }


    /**
     * Create the staff member.
     *
     * @return void
     */
    public function create()
    {
        $this->staff->admin = 0;
        $this->staff->firstname = '';

        ray('hit create');
        $this->validate();

        $this->staff->password = Hash::make($this->password);
        $this->staff->admin = (bool)$this->staff->admin;

        $brand = new \Lunar\Models\Brand([
            'name' => $this->staff->lastname,
            'branch_id' => $this->branch_id,
            'carrier_id' => $this->carrier_id,
            'plan_id' => $this->plan_id,
        ]);
        $brand->save();
        $this->staff->brand_id = $brand->id;

        $this->staff->save();
        $this->staffPermissions = collect(['catalogue:manage-products', 'reports:reports', 'deliveries:deliveries', 'vendor-settings:vendor-settings', 'catalogue:manage-orders']);

        // $this->staff->staffPermissions=['catalogue:manage-products'=>true,];
        $this->syncPermissions();

        $this->notify('ベンダーを追加しました', 'hub.vendors.index');
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

        return view('adminhub::livewire.components.vendors.create', [
            'firstPartyPermissions' => $permissions->filter(fn($permission) => (bool)$permission->firstParty),
        ])->layout('adminhub::layouts.base');
    }
}
