<?php

namespace Lunar\Hub\Http\Livewire\Components\Products;

use Lunar\Models\Product;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;
use Lunar\Models\TaxClass;

class ProductCreate extends AbstractProduct
{
    public $error_message = null;

    /**
     * Called when the component is mounted.
     *
     * @return void
     */
    public function mount()
    {

        if (isset(\Auth::user()->brand_id)) {
            //ベンダーは15件のみ登録可能
            $product_count = Product::where('brand_id', \Auth::user()->brand_id)
                ->whereNull('deleted_at')->count();
            ray($product_count);

            //  ray(\Auth::user()->brand->plan_id)->label('dfdfd');
            if (is_null(\Auth::user()->brand->plan_id)) {
                $this->notify(
                    'プランが未設定です。',
                    level: 'error'
                );
                $this->error_message = "プランが未設定です";
            } else {
                $vendor_plans = \App\Models\VendorPlan::find(\Auth::user()->brand->plan_id);

                if ($product_count >= $vendor_plans->limit) {
                    $this->notify(
                        '現状のプランでは商品は' . $vendor_plans->limit . '件のみ登録可能です。',
                        level: 'error'
                    );
                    $this->error_message = '現状のプランでは商品は' . $vendor_plans->limit . '件のみ登録可能です。';
                }
            }

        }

        $this->product = new Product([
            'status' => 'draft',
            'product_type_id' => ProductType::first()->id,
            'brand_id' => \Auth::user()->brand_id,
        ]);

        $this->options = collect();
        $this->variantsEnabled = $this->getVariantsCount() > 1;
        $this->variant = new ProductVariant([
            'purchasable' => 'always',
            'tax_class_id' => TaxClass::getDefault()?->id,
            'shippable' => true,
            'stock' => 0,
            'unit_quantity' => 1,
            'backorder' => 0,
        ]);

        $this->variantAttributes = $this->parseAttributes(
            $this->availableVariantAttributes,
            $this->variant->attribute_data,
            'variantAttributes',
        );

        $this->syncAvailability();
        $this->syncAssociations();
        $this->syncCollections();
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {

        return view('adminhub::livewire.components.products.create')
            ->layout('adminhub::layouts.base');
    }

    protected function getSlotContexts()
    {
        return ['product.all', 'product.create'];
    }
}
