<?php

namespace Lunar\Hub\Http\Livewire\Components\Reports;

use Closure;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
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

class ReportsIndex extends Component implements Tables\Contracts\HasTable
{

    use Tables\Concerns\InteractsWithTable;

    public array $data_list = [
        'calc_columns' => [
            'total.value',

            'commission',
            'payment',
        ],
        'calc2_columns' => [
            'quantity',
            'count',

        ],
    ];

    protected $listeners = ['export'];


    public function mount()
    {

    }

    protected function getTableQuery()
    {

        if (\Auth::user()->brand_id) {
            ray('brand_id');
            return \App\Models\OrderLine::select(
                DB::raw('MIN(lunar_order_lines.id) as id'),
                DB::raw('sum(lunar_order_lines.total) as total'),
                DB::raw('sum(lunar_order_lines.quantity) as quantity'),
                DB::raw('count(*) as count'),
                DB::raw('lunar_order_lines.brand_id'),
                DB::raw('lunar_brands.carrier_id as carrier_id')
            )
                ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
                ->join('lunar_brands', 'lunar_brands.id', '=', 'lunar_orders.brand_id')
                ->where('type', '=', 'physical')
                ->where('lunar_order_lines.brand_id', '=', \Auth::user()->brand_id)
                ->orderBy('lunar_order_lines.brand_id', 'DESC')
                ->groupBy('lunar_order_lines.brand_id', 'lunar_brands.carrier_id');
        }

        return \App\Models\OrderLine::
        select(
            DB::raw('MIN(lunar_order_lines.id) as id'),
            DB::raw('sum(lunar_order_lines.total) as total'),
            DB::raw('sum(lunar_order_lines.quantity) as quantity'),
            DB::raw('count(*) as count'),
            DB::raw('lunar_order_lines.brand_id'),
            DB::raw('lunar_brands.carrier_id as carrier_id')
        )
            ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
            ->join('lunar_brands', 'lunar_brands.id', '=', 'lunar_orders.brand_id')
            ->where('type', '=', 'physical')
            ->orderBy('lunar_order_lines.brand_id', 'DESC')
            ->groupBy('lunar_order_lines.brand_id', 'lunar_brands.carrier_id');
        // return \App\Models\OrderLine::query()->with(['purchasable'])->groupBy('brand_id')->where('type', '=','physical');
    }

    protected function getTableColumns(): array
    {

        return [

            //Tables\Columns\TextColumn::make('id')->label('id')->alignCenter(),
            Tables\Columns\TextColumn::make('brand_id')->formatStateUsing(function ($state, $record) {
                return \Lunar\Models\Brand::find($record->brand_id)->name;
            })->label('販売元')->alignRight(),

            Tables\Columns\TextColumn::make('count')->label('販売回数')->alignRight(),
            Tables\Columns\TextColumn::make('quantity')->label('個数')->alignRight(),


            Tables\Columns\TextColumn::make('total.value')
//                ->getStateUsing(function ( $record ) {
//                    $original_total=0;
//                    $record->total= $record->total->value;
//
//                   // return number_format($original_total).'円';
//                })
                ->formatStateUsing(function ($state, $record) {
                    $original_total = 0;
                    if (isset($record->total->value)) {
                        $original_total = $record->total->value;
                        $record->total = $record->total->value;
                    }
                    return number_format($original_total) . '円';
                })->label('税込合計')->alignRight(),

            Tables\Columns\TextColumn::make('commission')->formatStateUsing(function ($state, $record) {
                $commission = 0;
                if (isset($record->total->value)) {
                    $commission = round($record->total->value / 100 * 10);
                    $record->commission = $commission;
                    $record->payment = $record->total->value - $commission;
                }
                return number_format($commission) . '円';
            })->label('手数料(10%)')->alignRight(),
            Tables\Columns\TextColumn::make('carrier_id')->label('指定配送業者')
                ->formatStateUsing(
                    function ($state) {
                        $carrier = \Lunar\Hub\Models\Staff::find($state);
                        if ($carrier) {
                            return $carrier->lastname;
                        }
                        return '';
                    }
                )->alignCenter(),
            Tables\Columns\TextColumn::make('payment')->formatStateUsing(function ($state, $record) {
                return number_format($state) . '円';
            })->label('販売元手取分')->alignRight(),


//            The only way to align Column is
//        ->extraAttributes(['class' => 'flex flex-col items-end'])
//        ->alignRight() Only works on Aligning stacked content

//            Tables\Columns\BadgeColumn::make('status')
//                ->colors([
//                    'danger' => 'draft',
//                    'warning' => 'reviewing',
//                    'success' => 'published',
//                ]),
            //    Tables\Columns\IconColumn::make('is_featured')->boolean(),
        ];
    }

    protected function getTableActions(): array
    {
        return [


            //  Tables\Actions\ActionGroup::make([
            Tables\Actions\Action::make('order-line')
                ->label('販売商品一覧')
                ->url(
                    function ($record) {
                        return '/hub/reports/order-line/' . $record->brand_id . '/' . $this->tableFilters['lunar_orders']['placed_at']['value'];

                    }
                )->size('sm')
                ->icon('heroicon-o-link'),
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            //   ]),
        ];
    }

    public function export()
    {


        $data = [$this->getHeadings()];
        //   $order_reports = $this->getTableQuery()->get()->map(
        ray($this->applyFiltersToTableQuery($this->getTableQuery()));
        ray($this->getTableQuery())->red()->label('getTableQuery');
        ray($this->getTableFilters())->red()->label('getTableFilters');
        ray($this->getTableQuery());
        $order_reports = $this->applyFiltersToTableQuery($this->getTableQuery())->get()->map(
            function ($record) {
                $commission = 0;
                $payment = 0;
                $carrier_name = '';
                if (isset($record->total->value)) {
                    $commission = round($record->total->value / 100 * 10);
                    $payment = $record->total->value - $commission;
                }
                $carrier = \Lunar\Hub\Models\Staff::find($record->carrier_id);
                if ($carrier) {
                    $carrier_name = $carrier->lastname;
                }
                return collect([
                    '"' . \Lunar\Models\Brand::find($record->brand_id)->name . '"',
                    $record->count,
                    $record->quantity,
                    $record->total,
                    $commission,
                    '"' . $carrier_name . '"',
                    $payment,
                ])->join(',');
            })->toArray();
        ray($order_reports);
//        $orders = Order::findMany($orderIds)->map(function ($order) {
//            return collect([
//                $order->id,
//                $order->status,
//                $order->reference,
//                $order->billingAddress->full_name,
//                $order->total->decimal,
//                $order->created_at->format('Y-m-d'),
//                $order->created_at->format('H:ma'),
//            ])->join(',');
//        })->toArray();

        $data = collect(array_merge($data, $order_reports))->join("\n");
//        return response()->streamDownload(function () {
//            echo 'CSV Contents...';
//        }, 'export.csv');
        Storage::put('report_export.csv', $data);

        return Storage::download('report_export.csv');
    }

    public function getHeadings()
    {
        return collect([
            '"販売元"',
            '"販売回数"',
            '"個数"',
            '"税込合計"',
            '"手数料(7%)"',
            '"指定配送業者"',
            '"販売元手取分"',
        ])->join(',');
    }

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


    protected function getTableContentFooter()
    {
        if (\Auth::user()->brand_id) {
            return null;
        }
        return view('adminhub::livewire.components.reports.table-footer', $this->data_list);
    }

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

            SelectFilter::make('lunar_orders.placed_at')
                ->options($dates)
                ->query(function (Builder $query, array $data) use ($dates): Builder {

                    if (isset($this->tableFilters['lunar_orders'])) {
                        $data = $this->tableFilters['lunar_orders']['placed_at']['value'];
                        ray($this->tableFilters['lunar_orders'])->red()->label('data');
                    }

                    // ray($dates)->red()->label('$dates');
                    // ray($dates)->red()->label('$dates');
                    if (empty($data)) {
                        $data = Carbon::now();
                        //    $data=end($dates) ;
                        //  $data=$dates['2023-04'];
                    }
                    // ray($query)->red()->label('$query');
                    $start = Carbon::create($data);
                    $monthStart = $start->startOfMonth()->toDateString(); // get first day of the month
                    $monthEnd = $start->endOfMonth()->toDateString(); // get last day of the month

                    //ray($monthStart)->red()->label('$monthStart');
                    // ray($monthEnd)->red()->label('$monthEnd');

                    return $query
                        ->when(
                            $monthStart,
                            fn(Builder $query, $date): Builder => $query->whereDate('lunar_orders.placed_at', '>=', $date),
                        )
                        ->when(
                            $monthEnd,
                            fn(Builder $query, $date): Builder => $query->whereDate('lunar_orders.placed_at', '<=', $date),
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
//        return view('adminhub::livewire.pages.reports.index')
//            ->layout('adminhub::layouts.app', [
//                'title' => 'レポート',
//            ]);
        return view('adminhub::livewire.components.reports.index')
            ->layout('adminhub::layouts.app', [
                'title' => 'レポート',
            ]);
    }
}
