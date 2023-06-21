<?php

namespace Lunar\Hub\Http\Livewire\Components\Deliveries;

use Closure;
use DB;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Filament\Tables;
use Illuminate\Contracts\Pagination\Paginator;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Webbingbrasil\FilamentDateFilter\DateFilter;

use Carbon\Carbon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Layout;
use Filament\Forms;
use Filament\Tables\Filters\Filter;

class DeliveriesIndex extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

//    public array $data_list = [
//        'calc_columns' => [
//            'total.value',
//            'commission',
//            'payment',
//        ],
//        'calc2_columns' => [
//            'quantity',
//            'count',
//        ],
//    ];

    public function mount()
    {


    }

    protected function getTableQuery(): Builder
    {
        if (\Auth::user()->brand_id) {
            return \App\Models\OrderLine::query()->with(['purchasable', 'order', 'order.shippingAddress'])
                // ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
                ->where('type', '=', 'physical')
                ->where('brand_id', '=', \Auth::user()->brand_id);
        }
        return \App\Models\OrderLine::query()->with(['purchasable', 'order'])
            // ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
            ->where('type', '=', 'physical');
    }

    protected function getTableColumns(): array
    {
        return [


            Split::make([
                ToggleColumn::make('is_shipped'),
//                ToggleColumn::make('is_shipped_str')->formatStateUsing(function ($state, $record) {
//                    if ($record->is_shipped == 0) {
//                        return "未発送";
//                    }
//                    return "発送済";
//                })->label('発送状態')->alignCenter(),
                BadgeColumn::make('is_shipped_badge')
                    ->getStateUsing(function ($record) {
                        if ($record->is_shipped == 0) {
                            return "未発送";
                        }
                        return "発送済";
                    })
                    ->colors([
                        'primary',
                        'secondary' => static fn($state): bool => $state === '未発送',
                        //'warning' => static fn ($record): bool => $record->is_shipped === '発送済',
                        'success' => static fn($state): bool => $state === '発送済',
                        // 'danger' => static fn ($record): bool => $state === 'rejected',
                    ]),
                //            Tables\Columns\IconColumn::make('notification_icon')
//                ->options([
//            'heroicon-o-link',
//                ]),
                // Tables\Columns\TextColumn::make('id')->label('注文行ID')->alignCenter(),
                Tables\Columns\TextColumn::make('order.reference')->label('注文コード')->alignCenter(),
                Tables\Columns\TextColumn::make('purchasable.product.brand.name')->label('販売元')->alignCenter(),
                // Tables\Columns\TextColumn::make('purchasable.product.brand.id')->label('販売元ID')->alignCenter(),
                Tables\Columns\ImageColumn::make('sfafa')->label('画像')->getStateUsing(
                    function ($record) {
                        //  ray($record->product2)->red();
                        //ray($record->purchasable)->red();
                        if ($record->type === 'physical') {
                            $product_variant = \Lunar\Models\ProductVariant::find($record->purchasable_id);
                            //$unit_price = $product_variant->getThumbnail();
                            return $product_variant->getThumbnail()->original_url;
                        }
                        return '';
                    }
                )->size(60)->circular(),
                Tables\Columns\TextColumn::make('purchasable.sku')->label('SKU')->description(function ($record) {
                    return $record->option;
                }),
                Tables\Columns\TextColumn::make('description')->label('商品名')->description(function ($record) {
                    return $record->option;
                })->limit(40),
                Tables\Columns\TextColumn::make('unit_price')->formatStateUsing(function ($state, $record) {
                    $unit_price = 0;
                    if (isset($record->unit_price->value)) {
                        $unit_price = $record->unit_price->value;
                    }
                    return number_format($unit_price * 1.1) . '円';
                })->label('税込単価')->alignRight(),
                Tables\Columns\TextColumn::make('quantity')->label('個数')->alignRight(),
                Tables\Columns\TextColumn::make('total')->formatStateUsing(function ($state, $record) {
                    $original_total = 0;
                    if (isset($record->total->value)) {
                        $original_total = $record->total->value;
                    }
                    // $original_total = $record->total->value;
                    return number_format($original_total) . '円';
                    //return $original_total;
                })->label('税込合計')->alignRight(),
            ]),
            Panel::make([
                Stack::make([
                    TextColumn::make('order.shippingAddress.last_name')->formatStateUsing(function ($state, $record) {
                        return $record->order->shippingAddress->last_name . $record->order->shippingAddress->first_name;
                    }),
                    //TextColumn::make('order.shippingAddress.first_name'),
//                    TextColumn::make('order.shippingAddress.contact_email')->formatStateUsing(function ($state, $record) {
//                        return "Email:" . $record->order->shippingAddress->contact_email;
//                    }),
                    TextColumn::make('order.shippingAddress.contact_phone')->formatStateUsing(function ($state, $record) {
                        return "Tel:" . $record->order->shippingAddress->contact_phone;
                    }),
                    TextColumn::make('order.shippingAddress.postcode')->formatStateUsing(function ($state, $record) {
                        return "〒:" . $record->order->shippingAddress->postcode;
                    }),
                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.state')->formatStateUsing(function ($state, $record) {
                        return $record->order->shippingAddress->state . $record->order->shippingAddress->city . $record->order->shippingAddress->line_one . " " . $record->order->shippingAddress->line_two . " " . $record->order->shippingAddress->line_three;
                    }),
                    // \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.city'),
//                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.line_one'),
//                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.line_two'),
//                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.line_three'),
                ]),
            ])->collapsible(false),


        ];
    }

    protected function getTableActions(): array
    {
        return [
            //  Tables\Actions\ActionGroup::make([
            Tables\Actions\Action::make('product_page')
                ->label('商品ページ')
                ->url(
                    function ($record) {
                        if ($record->type === 'physical') {
                            return '/products/' . $record->purchasable->product->id;
                        }
                        return '';
                    }
                )->size('sm')
                ->icon('heroicon-o-link'),
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            //   ]),
        ];
    }

//    protected function getTableBulkActions(): array
//    {
//        return [
//            BulkAction::make('delete')
//                ->action(fn(Collection $records) => $records->each->delete())
//        ];
//    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }
//    protected function getTableRecordsPerPageSelectOptions(): array
//    {
//        return [10, 25, 50, 100];
//    }

//    protected function paginateTableQuery(Builder $query): Paginator
//    {
//        return $query->simplePaginate($this->getTableRecordsPerPage() == -1 ? $query->count() : $this->getTableRecordsPerPage());
//    }


//    protected function getTableContentFooter()
//    {
//        if (\Auth::user()->brand_id) {
//            return null;
//        }
//        return view('adminhub::livewire.components.deliveries.table-footer', $this->data_list);
//    }

    protected function getTableFilters(): array
    {
        //セレクトオプションを作成
        $start = Carbon::create(2023, 4);//開始年月
        $end = Carbon::now();
        $dates = [];

        while ($start->lte($end)) {
            $dates[$start->format('Y-m')] = $start->format('Y年n月');
            $start->addMonth();
        }

        return [
            SelectFilter::make('brand_id')
                ->label('販売元')
                ->relationship('brand',
                    'name', function (Builder $query) {
                        if (\Auth::user()->brand_id) {
                            return $query->where('id', \Auth::user()->brand_id);
                        }
                        return $query;
                    }
                )
                ->default(function () {
                    if (isset($this->tableFilters['brand_id'])) {
                        ray($this->tableFilters['brand_id'])->red()->label('brand_id');
                        return $this->tableFilters['brand_id']['value'];
                    }
                    return null;
                }),

            SelectFilter::make('order.placed_at')
                ->options($dates)
                ->query(function (Builder $query, array $data) use ($dates): Builder {

                    if (isset($this->tableFilters['order'])) {
                        $data = $this->tableFilters['order']['placed_at']['value'];
                        ray($this->tableFilters['order'])->red()->label('data');
                    }

                    ray($dates)->red()->label('$dates');
                    // ray($dates)->red()->label('$dates');
                    if (empty($data)) {
                        $data = Carbon::now();
                        //    $data=end($dates) ;
                        //  $data=$dates['2023-04'];
                    }
                    ray($query)->red()->label('$query');
                    $start = Carbon::create($data);
                    $monthStart = $start->startOfMonth()->toDateString(); // get first day of the month
                    $monthEnd = $start->endOfMonth()->toDateString(); // get last day of the month

                    ray($monthStart)->red()->label('$monthStart');
                    ray($monthEnd)->red()->label('$monthEnd');

                    return $query->whereHas(
                        'order',
                        fn(Builder $query) => $query
                            ->when(
                                $monthStart,
                                fn(Builder $query, $date): Builder => $query->whereDate('lunar_orders.placed_at', '>=', $date),
                            )
                            ->when(
                                $monthEnd,
                                fn(Builder $query, $date): Builder => $query->whereDate('lunar_orders.placed_at', '<=', $date),
                            )
                    );
                })->default(function () use ($dates) {
                    return array_key_last($dates);
                })->label('年月'),
        ];
    }

    protected function getTableFiltersLayout(): ?string
    {
        return Layout::AboveContent;
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
//        return view('adminhub::livewire.pages.deliveries.index')
//            ->layout('adminhub::layouts.app', [
//                'title' => 'レポート',
//            ]);
        return view('adminhub::livewire.components.deliveries.index')
            ->layout('adminhub::layouts.app', [
                'title' => '配送管理',
            ]);
    }
}
