<?php

namespace Lunar\Hub\Auth;

use Illuminate\Support\Collection;

class Manifest
{
    /**
     * A collection of permissions loaded into the manifest.
     */
    protected Collection $permissions;

    /**
     * Initialise the manifest class.
     */
    public function __construct()
    {
        $this->permissions = collect($this->getBasePermissions());
    }

    /**
     * Returns all permissions loaded in the manifest.
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    /**
     * Returns permissions grouped by their handle
     * For example, settings:channel would become a child of settings.
     */
    public function getGroupedPermissions(): Collection
    {
        $permissions = clone $this->permissions;

        foreach ($permissions as $key => $permission) {
            $parent = $this->getParentPermission($permission);

            if ($parent) {
                $parent->children->push($permission);
                $permissions->forget($key);
            }
        }

        return $permissions;
    }

    /**
     * Returns the parent permission based on handle naming.
     *
     * @return null|\Lunar\Hub\Acl\Permission
     */
    protected function getParentPermission(Permission $permission)
    {
        $crumbs = explode(':', $permission->handle);

        if (empty($crumbs[1])) {
            return null;
        }

        return $this->permissions->first(fn($parent) => $parent->handle === $crumbs[0]);
    }

    /**
     * Adds a permission to the manifest if it doesn't already exist.
     *
     * @return void
     */
    public function addPermission(\Closure $callback)
    {
        $permission = new Permission();
        $callback($permission);

        $permission->firstParty(false);

        // Do we already have a permission with this handle?
        $existing = $this->permissions->first(fn($p) => $p->handle == $permission->handle);

        if (!$existing) {
            $this->permissions->push($permission);
        }
    }

    /**
     * Returns the base permissions which are required by Lunar.
     */
    protected function getBasePermissions(): array
    {
        return [
            new Permission(
                __('スライダー管理'),
                'sliders:sliders',
                __('スライダー管理')
            ),

            new Permission(
                __('adminhub::auth.permissions.settings.name'),
                'settings',
                __('adminhub::auth.permissions.settings.description')
            ),
            new Permission(
                __('adminhub::auth.permissions.settings.core.name'),
                'settings:core',
                __('adminhub::auth.permissions.settings.core.description')
            ),
            new Permission(
                __('adminhub::auth.permissions.settings.staff.name'),
                'settings:manage-staff',
                __('adminhub::auth.permissions.settings.staff.description')
            ),
            new Permission(
                __('支部'),
                'settings:manage-branches',
                __('支部')
            ),
            new Permission(
                __('ベンダー'),
                'vendors:vendors',
                __('ベンダー')
            ),
            new Permission(
                __('物流業者'),
                'carriers:carriers',
                __('物流業者')
            ),
            //配送管理
            new Permission(
                __('配送管理'),
                'deliveries:deliveries',
                __('配送管理')
            ),
            //レポート
            new Permission(
                __('レポート'),
                'reports:reports',
                __('レポート')
            ),

            //ポイント
            new Permission(
                __('ポイント管理'),
                'points:points',
                __('ポイント管理')
            ),


            //ポイント
            new Permission(
                __('販売者設定'),
                'vendor-settings:vendor-settings',
                __('販売者設定')
            ),
            new Permission(
                __('adminhub::auth.permissions.settings.attributes.name'),
                'settings:manage-attributes',
                __('adminhub::auth.permissions.settings.attributes.description')
            ),
            new Permission(
                __('adminhub::auth.permissions.catalogue.products.name'),
                'catalogue:manage-products',
                __('adminhub::auth.permissions.catalogue.products.description')
            ),
            new Permission(
                __('adminhub::auth.permissions.catalogue.collections.name'),
                'catalogue:manage-collections',
                __('adminhub::auth.permissions.catalogue.collections.description')
            ),
            new Permission(
                __('adminhub::auth.permissions.catalogue.orders.name'),
                'catalogue:manage-orders',
                __('adminhub::auth.permissions.catalogue.orders.description')
            ),
            new Permission(
                __('adminhub::auth.permissions.catalogue.customers.name'),
                'catalogue:manage-customers',
                __('adminhub::auth.permissions.catalogue.customers.description')
            ),
            new Permission(
                __('adminhub::auth.permissions.discounts.name'),
                'catalogue:manage-discounts',
                __('adminhub::auth.permissions.discounts.description')
            ),
        ];
    }
}
