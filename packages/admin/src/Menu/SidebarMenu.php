<?php

namespace Lunar\Hub\Menu;

use Lunar\Hub\Facades\Menu;

final class SidebarMenu
{
    /**
     * Make menu.
     *
     * @return void
     */
    public static function make()
    {
        (new static())
            ->makeTopLevel()
            ->addSections();
    }

    /**
     * Make top level navigation.
     *
     * @return static
     */
    protected function makeTopLevel()
    {
        $slot = Menu::slot('sidebar');

        $slot->addItem(function ($item) {
            $item
                ->name(__('adminhub::menu.sidebar.index'))
                ->handle('hub.index')
                ->route('hub.index')
                ->icon('chart-square-bar');
        });

        return $this;
    }

    /**
     * Add our menu sections.
     *
     * @return static
     */
    protected function addSections()
    {
        $slot = Menu::slot('sidebar');

        $catalogueGroup = $slot
            ->group('hub.catalogue')
            ->name(__('adminhub::menu.sidebar.catalogue'));
        $salesGroup = $slot
            ->group('hub.sales')
            ->name(__('adminhub::menu.sidebar.sales'));

        $productGroup = $catalogueGroup
            ->section('hub.products')
            ->name(__('adminhub::menu.sidebar.products'))
            ->handle('hub.products')
            ->route('hub.products.index')
            ->icon('shopping-bag');
        $catalogueGroup
            ->section('hub.collections')
            ->name(__('adminhub::menu.sidebar.collections'))
            ->handle([
                'hub.collection-groups',
                'hub.collections',
            ])
            ->route('hub.collection-groups.index')
            ->icon('collection');


        $vendorGroup = $slot
            ->group('hub.vendors')
            ->name('ベンダー');
        $vendorGroup->addItem(function ($menuItem) {
            $menuItem
                ->name('ベンダー管理')
                ->handle('hub.vendors')
                ->route('hub.vendors.index');
        });


        $deliveryGroup = $slot
            ->group('hub.deliveries')
            ->name('配送管理');
        $deliveryGroup->addItem(function ($menuItem) {
            $menuItem
                ->name('配送')
                ->handle('hub.deliveries')
                ->icon('truck')
                ->route('hub.deliveries.index');
        });

        $reportGroup = $slot
            ->group('hub.reports')
            ->name('レポート');
        $reportGroup->addItem(function ($menuItem) {
            $menuItem
                ->name('レポート')
                ->handle('hub.reports')
                ->icon('chart-bar')
                ->route('hub.reports.index');
        });

        $reportGroup = $slot
            ->group('hub.points')
            ->name('ポイント管理');
        $reportGroup->addItem(function ($menuItem) {
            $menuItem
                ->name('ポイント交換依頼')
                ->handle('hub.points')
                ->icon('arrows-right-left')
                ->route('hub.points.index');
        });

        $productGroup->addItem(function ($menuItem) {
            $menuItem
                ->name(__('adminhub::menu.sidebar.product-types'))
                ->handle('hub.product-types')
                ->route('hub.product-types.index');
        });

//        $productGroup->addItem(function ($menuItem) {
//            $menuItem
//                ->name(__('adminhub::menu.sidebar.brands'))
//                ->handle('hub.brands')
//                ->route('hub.brands.index');
//        });


        $salesGroup->addItem(function ($menuItem) {
            $menuItem
                ->name(__('adminhub::menu.sidebar.orders'))
                ->handle('hub.orders')
                ->route('hub.orders.index')
                ->icon('cash');
        });

        $salesGroup->addItem(function ($menuItem) {
            $menuItem
                ->name(__('adminhub::menu.sidebar.customers'))
                ->handle('hub.customers')
                ->route('hub.customers.index')
                ->icon('users');
        });

//        $salesGroup->addItem(function ($menuItem) {
//            $menuItem
//                ->name('レポート')
//                ->handle('hub.reports')
//                ->route('hub.reports.index')
//                ->icon('chart-bar');
//        });

        $salesGroup->addItem(function ($menuItem) {
            $menuItem
                ->name(__('adminhub::menu.sidebar.discounts'))
                ->handle('hub.discounts')
                ->route('hub.discounts.index')
                ->icon('ticket');
        });

        return $this;
    }
}
