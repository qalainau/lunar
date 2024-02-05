<?php

namespace Lunar\Hub\Http\Livewire\Components\Deliveries;

use Closure;
use DB;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Filament\Tables;
use Illuminate\Contracts\Pagination\Paginator;
use Mail;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Webbingbrasil\FilamentDateFilter\DateFilter;

use Carbon\Carbon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Layout;
use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Lunar\Hub\Http\Livewire\Traits\Notifies;

class DeliveriesIndex extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;
    use Notifies;

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
    public string $contact_email;

    public function mount()
    {


    }

//    protected function getTableQuery(): Builder
//    {
//        if (\Auth::user()->brand_id) {
//            return \App\Models\OrderLine::query()->with(['purchasable', 'order', 'order.shippingAddress'])
//                // ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
//                ->where('type', '=', 'physical')
//                ->where('brand_id', '=', \Auth::user()->brand_id);
//        } elseif (\Auth::user()->is_carrier) {
//            //配送業者に紐づいているベンダーのリスト
//            $vendors = \Lunar\Models\Brand::where('carrier_id', \Auth::user()->id)->get();
//            $brand_ids = [];
//            foreach ($vendors as $vendor) {
//                $brand_ids[] = $vendor->id;
//            }
//            ray(\Auth::user()->id)->red()->label('auth_id');//20
//            ray($brand_ids)->red()->label('brand_ids');
//            return \App\Models\OrderLine::query()->with(['purchasable', 'order', 'order.shippingAddress'])
//                // ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
//                ->where('type', '=', 'physical')
//                ->whereIn('brand_id', $brand_ids);
//        }
//        return \App\Models\OrderLine::query()->with(['purchasable', 'order'])
//            // ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
//            ->where('type', '=', 'physical');
//    }

    protected function getTableQuery(): Builder
    {
        if (\Auth::user()->brand_id) {
            return \App\Models\Order::query()->with(['physicalLines', 'shippingAddress'])
                // ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
                //->where('type', '=', 'physical')
                // ->whereIn('status', ['dispatched', 'paid'])
                ->where('brand_id', '=', \Auth::user()->brand_id);
        } elseif (\Auth::user()->is_carrier) {
            //配送業者に紐づいているベンダーのリスト
            $vendors = \Lunar\Models\Brand::where('carrier_id', \Auth::user()->id)->get();
            $brand_ids = [];
            foreach ($vendors as $vendor) {
                $brand_ids[] = $vendor->id;
            }
            ray(\Auth::user()->id)->red()->label('auth_id');//20
            ray($brand_ids)->red()->label('brand_ids');
            return \App\Models\Order::query()->with(['physicalLines', 'shippingAddress'])
                // ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
                // ->where('lunar_order_lines.type', '=', 'physical')

                // ->whereIn('status', ['dispatched', 'paid'])
                ->whereIn('brand_id', $brand_ids);
        }
        return \App\Models\Order::query()->with(['physicalLines', 'shippingAddress']);
        // ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')

        //->where('type', '=', 'physical');
    }

//    protected function getTableColumns(): array
//    {
//        return [
//
//
//            Split::make([
//                ToggleColumn::make('is_shipped'),
////                ToggleColumn::make('is_shipped_str')->formatStateUsing(function ($state, $record) {
////                    if ($record->is_shipped == 0) {
////                        return "未発送";
////                    }
////                    return "発送済";
////                })->label('発送状態')->alignCenter(),
//                BadgeColumn::make('is_shipped_badge')
//                    ->getStateUsing(function ($record) {
//                        if ($record->is_shipped == 0) {
//                            return "未発送";
//                        }
//                        return "発送済";
//                    })
//                    ->colors([
//                        'primary',
//                        'secondary' => static fn($state): bool => $state === '未発送',
//                        //'warning' => static fn ($record): bool => $record->is_shipped === '発送済',
//                        'success' => static fn($state): bool => $state === '発送済',
//                        // 'danger' => static fn ($record): bool => $state === 'rejected',
//                    ]),
//                //            Tables\Columns\IconColumn::make('notification_icon')
////                ->options([
////            'heroicon-o-link',
////                ]),
//                // Tables\Columns\TextColumn::make('id')->label('注文行ID')->alignCenter(),
//                Tables\Columns\TextColumn::make('order.reference')->label('注文コード')->alignCenter(),
//                Tables\Columns\TextColumn::make('purchasable.product.brand.name')->label('販売元')->alignCenter(),
//                // Tables\Columns\TextColumn::make('purchasable.product.brand.id')->label('販売元ID')->alignCenter(),
//                Tables\Columns\ImageColumn::make('sfafa')->label('画像')->getStateUsing(
//                    function ($record) {
//                        //  ray($record->product2)->red();
//                        //ray($record->purchasable)->red();
//                        if ($record->type === 'physical') {
//                            $product_variant = \Lunar\Models\ProductVariant::find($record->purchasable_id);
//                            //$unit_price = $product_variant->getThumbnail();
//                            return $product_variant->getThumbnail()->original_url;
//                        }
//                        return '';
//                    }
//                )->size(60)->circular(),
//                Tables\Columns\TextColumn::make('purchasable.sku')->label('SKU')->description(function ($record) {
//                    return $record->option;
//                }),
//                Tables\Columns\TextColumn::make('description')->label('商品名')->description(function ($record) {
//                    return $record->option;
//                })->limit(40),
//                Tables\Columns\TextColumn::make('unit_price')->formatStateUsing(function ($state, $record) {
//                    $unit_price = 0;
//                    if (isset($record->unit_price->value)) {
//                        $unit_price = $record->unit_price->value;
//                    }
//                    return number_format($unit_price * 1.1) . '円';
//                })->label('税込単価')->alignRight(),
//                Tables\Columns\TextColumn::make('quantity')->label('個数')->alignRight(),
//                Tables\Columns\TextColumn::make('total')->formatStateUsing(function ($state, $record) {
//                    $original_total = 0;
//                    if (isset($record->total->value)) {
//                        $original_total = $record->total->value;
//                    }
//                    // $original_total = $record->total->value;
//                    return number_format($original_total) . '円';
//                    //return $original_total;
//                })->label('税込合計')->alignRight(),
//            ]),
//            Panel::make([
//                Stack::make([
//                    TextColumn::make('order.shippingAddress.last_name')->formatStateUsing(function ($state, $record) {
//                        return $record->order->shippingAddress->last_name . $record->order->shippingAddress->first_name;
//                    }),
//                    //TextColumn::make('order.shippingAddress.first_name'),
////                    TextColumn::make('order.shippingAddress.contact_email')->formatStateUsing(function ($state, $record) {
////                        return "Email:" . $record->order->shippingAddress->contact_email;
////                    }),
//                    TextColumn::make('order.shippingAddress.contact_phone')->formatStateUsing(function ($state, $record) {
//                        return "Tel:" . $record->order->shippingAddress->contact_phone;
//                    }),
//                    TextColumn::make('order.shippingAddress.postcode')->formatStateUsing(function ($state, $record) {
//                        return "〒:" . $record->order->shippingAddress->postcode;
//                    }),
//                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.state')->formatStateUsing(function ($state, $record) {
//                        return $record->order->shippingAddress->state . $record->order->shippingAddress->city . $record->order->shippingAddress->line_one . " " . $record->order->shippingAddress->line_two . " " . $record->order->shippingAddress->line_three;
//                    }),
//                    // \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.city'),
////                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.line_one'),
////                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.line_two'),
////                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.line_three'),
//                ]),
//            ])->collapsible(false),
//
//
//        ];
//    }


    protected function getTableColumns(): array
    {
        return [


            Split::make([
//                ToggleColumn::make('is_shipped')
//                    ->format
//                    ->updateStateUsing(
//                        function ($record, $state) {
//                            if ($state) {
//                                $record->status = 'dispatched';
//                                $record->save();
////                                Notification::make()
////                                    ->title('状態を発送済みに変更し、お客様にメールを送信しました。')
////                                    ->success()
////                                    ->send();
//
//                                $this->notify(
//                                    __('状態を発送済みに変更し、お客様にメールを送信しました。')
//                                );
//
//                            } else {
//                                $this->notify(
//                                    __('状態を発送済みからは変更できません。')
//                                );
//                                return false;
//                            }
//                            ray($record)->green();
//                            ray($state)->green();
//
//                            //return null;
//                        }
//                    ),

//                BadgeColumn::make('is_shipped_badge')
//                    ->getStateUsing(function ($record) {
//                        if ($record->is_shipped == 0) {
//                            return "未発送";
//                        }
//                        return "発送済";
//                    })
//                    ->colors([
//                        'primary',
//                        'secondary' => static fn($state): bool => $state === '未発送',
//                        //'warning' => static fn ($record): bool => $record->is_shipped === '発送済',
//                        'success' => static fn($state): bool => $state === '発送済',
//                        // 'danger' => static fn ($record): bool => $state === 'rejected',
//                    ]),

                // Tables\Columns\TextColumn::make('id')->label('注文行ID')->alignCenter(),

                SelectColumn::make('status')
                    ->options(
                        function ($record) {
                            if ($record->status == 'paid') {
                                return [
                                    'dispatched' => '発送済',
                                    'paid' => '決済完了',];
                            } elseif ($record->status == 'dispatched') {
                                return [
                                    'dispatched' => '発送済',
                                ];
                            }

                            return [
                                'dispatched' => '発送済',
                                'paid' => '決済完了',
                                'order-received' => '受注',
                                'slip-shipped' => '伝票郵送済',
                                'payment-received' => '注文確定',
                            ];

                        }
                    )
                    ->disableOptionWhen(
                        function ($record) {
                            if ($record->status == 'dispatched' || $record->status == 'order-received'
                                || $record->status == 'slip-shipped' || $record->status == 'payment-received') {
                                return true;
                            }
                            return false;
                        })
                    ->disablePlaceholderSelection()
                    //->updateState($state)
                    ->updateStateUsing(
                        function ($record, $state) {
                            ray($record)->green();
                            ray($state)->green();
                            if ($state == 'dispatched') {
                                $record->status = $state;
                                ray($record->tax_breakdown);
                                $record->tax_breakdown = $record->tax_breakdown->map(function ($tax) {
                                    return [
                                        'description' => $tax->description,
                                        'identifier' => $tax->identifier,
                                        'percentage' => $tax->percentage,
                                        'total' => $tax->total->value,
                                    ];
                                })->values();

                                try {
                                    $record->save();

                                    //ray($record->shippingAddress)->red();
                                    $this->contact_email = $record->shippingAddress->contact_email;
                                    Mail::send(['text' => 'emails.dispatched'], [
                                        "order" => $record,
                                        "content" => ''
                                    ], function ($message) {
                                        $message
                                            ->to($this->contact_email)
                                            //->bcc('admin@sample.com')
                                            ->subject("誓市場から商品発送のお知らせ");
                                    });

                                    $this->notify(
                                        __('状態を発送済みに変更し、お客様に発送通知メールを送信しました。')
                                    );

                                    return $state;
                                } catch (\Exception $e) {
                                    ray($e);
                                    $this->notify(
                                        __('状態変更およびメール送信に失敗しました')
                                    );

                                }

                            } else {
                                $this->notify(
                                    __('発送済み以外には変更できません。')
                                );
                                return $state;
                            }

//                        $this->notify(
//                            __('状態を' . $state . 'に変更しました。')
//                        );
                        }
                    )


                ,

//                BadgeColumn::make('status')
//                    ->getStateUsing(function ($record) {
//
//                        ray(config('lunar.orders.statuses'));
//                        $statuses = config('lunar.orders.statuses');
//                        ray($statuses);
//                        ray($statuses[$record->status]);
//                        //return $statuses[$record->status];
//                        return $statuses[$record->status]['label'];
//                    })
//                    ->colors([
//                        'primary',
//                        'secondary' => static fn($state): bool => $state === '決済完了 ',
//                        //'warning' => static fn ($record): bool => $record->is_shipped === '発送済',
//                        'success' => static fn($state): bool => $state === '発送済',
//                        // 'danger' => static fn ($record): bool => $state === 'rejected',
//                    ]),
                Tables\Columns\TextColumn::make('reference')->label('注文コード')->alignCenter(),
                Tables\Columns\TextColumn::make('brand.name')->label('販売元')->alignCenter(),
//                Tables\Columns\ImageColumn::make('sfafa')->label('画像')->getStateUsing(
//                    function ($record) {
//                        ray($record->physicalLines)->red();
//                        //ray($record->purchasable)->red();
//                        if ($record->type === 'physical') {
//                            $product_variant = \Lunar\Models\ProductVariant::find($record->purchasable_id);
//                            //$unit_price = $product_variant->getThumbnail();
//                            return $product_variant->getThumbnail()->original_url;
//                        }
//                        return '';
//                    }
//                )->size(60)->circular(),

                Tables\Columns\TextColumn::make('total')->formatStateUsing(function ($state, $record) {
                    $original_total = 0;
                    if (isset($record->total->value)) {
                        $original_total = $record->total->value;
                    }
                    return number_format($original_total) . '円';
                })->label('税込合計')->alignRight(),
            ]),
            Panel::make([
                View::make('vendor.adminhub.filament.deliveries-view'),

            ]),

            Panel::make([


                Stack::make([
                    TextColumn::make('shippingAddress.last_name')->formatStateUsing(function ($state, $record) {
                        return $record->shippingAddress->last_name . $record->shippingAddress->first_name;
                    }),
                    //TextColumn::make('order.shippingAddress.first_name'),
//                    TextColumn::make('order.shippingAddress.contact_email')->formatStateUsing(function ($state, $record) {
//                        return "Email:" . $record->order->shippingAddress->contact_email;
//                    }),
                    TextColumn::make('shippingAddress.contact_phone')->formatStateUsing(function ($state, $record) {
                        return "Tel:" . $record->shippingAddress->contact_phone;
                    }),
                    TextColumn::make('shippingAddress.postcode')->formatStateUsing(function ($state, $record) {
                        return "〒:" . $record->shippingAddress->postcode;
                    }),
                    \Filament\Tables\Columns\TextColumn::make('shippingAddress.state')->formatStateUsing(function ($state, $record) {
                        return $record->shippingAddress->state . $record->shippingAddress->city . $record->shippingAddress->line_one . " " . $record->shippingAddress->line_two . " " . $record->shippingAddress->line_three;
                    }),
                    // \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.city'),
//                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.line_one'),
//                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.line_two'),
//                    \Filament\Tables\Columns\TextColumn::make('order.shippingAddress.line_three'),
                ]),
            ])
                ->collapsible(false),


        ];
    }


    protected function getTableActions(): array
    {
        return [
            //  Tables\Actions\ActionGroup::make([
//            Tables\Actions\Action::make('product_page')
//                ->label('商品ページ')
//                ->url(
//                    function ($record) {
//                        if ($record->type === 'physical') {
//                            return '/products/' . $record->purchasable->product->id;
//                        }
//                        return '';
//                    }
//                )->size('sm')
//                ->icon('heroicon-o-link'),
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
                        } elseif (\Auth::user()->is_carrier) {
                            ray('-----------------------');
                            //配送業者に紐づいているベンダーのリスト
                            $vendors = \Lunar\Models\Brand::where('carrier_id', \Auth::user()->id)->get();
                            $brand_ids = [];
                            foreach ($vendors as $vendor) {
                                $brand_ids[] = $vendor->id;
                            }
                            return $query->whereIn('id', $brand_ids);
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

//            SelectFilter::make('order.placed_at')
//                ->options($dates)
//                ->query(function (Builder $query, array $data) use ($dates): Builder {
//
//                    if (isset($this->tableFilters['order'])) {
//                        $data = $this->tableFilters['order']['placed_at']['value'];
//                        ray($this->tableFilters['order'])->red()->label('data');
//                    }
//
//                    ray($dates)->red()->label('$dates');
//                    // ray($dates)->red()->label('$dates');
//                    if (empty($data)) {
//                        $data = Carbon::now();
//                        //    $data=end($dates) ;
//                        //  $data=$dates['2023-04'];
//                    }
//                    ray($query)->red()->label('$query');
//                    $start = Carbon::create($data);
//                    $monthStart = $start->startOfMonth()->toDateString(); // get first day of the month
//                    $monthEnd = $start->endOfMonth()->toDateString(); // get last day of the month
//
//                    ray($monthStart)->red()->label('$monthStart');
//                    ray($monthEnd)->red()->label('$monthEnd');
//
//                    return $query->whereHas(
//                        'order',
//                        fn(Builder $query) => $query
//                            ->when(
//                                $monthStart,
//                                fn(Builder $query, $date): Builder => $query->whereDate('lunar_orders.placed_at', '>=', $date),
//                            )
//                            ->when(
//                                $monthEnd,
//                                fn(Builder $query, $date): Builder => $query->whereDate('lunar_orders.placed_at', '<=', $date),
//                            )
//                    );
//                })
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

                    return $query->where(
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
                })
                ->default(function () use ($dates) {
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
