<?php

namespace Lunar\Hub\Http\Livewire\Components\Points;

use Closure;
use DB;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Fieldset;

class PointsIndex extends Component implements Tables\Contracts\HasTable
{

    use Tables\Concerns\InteractsWithTable;


    public function mount()
    {


    }

    protected function getTableQuery()
    {
        //if(isset($tableF))
        return \App\Models\PointExchange::query()->orderBy('requested_at', 'desc');
    }

    protected function getTableColumns(): array
    {

        return [

            //\Filament\Tables\Columns\ToggleColumn::make('status'),


            //Tables\Columns\TextColumn::make('id')->label('id')->alignCenter(),
//            use Filament\Tables\Columns\Layout\Panel;
//            use Filament\Tables\Columns\TextColumn;
//
//use Filament\Tables\Columns\Layout\Split;
//use Filament\Tables\Columns\Layout\Stack;


//            Tables\Columns\TextColumn::make('exchanged_at')->formatStateUsing(function ($state, $record) {
//                return number_format($state).'円';
//            })->label('依頼日')->alignCenter(),

//            \Filament\Tables\Columns\Layout\Panel::make([
//                \Filament\Tables\Columns\Layout\Stack::make([
//                    \Filament\Tables\Columns\TextColumn::make('email'),
//                    \Filament\Tables\Columns\TextColumn::make('phone'),
//                ]),
//            ])->collapsed(false),
            \Filament\Tables\Columns\Layout\Split::make([

                \Filament\Tables\Columns\SelectColumn::make('status')->label('変更')
                    ->options([
                        '申請' => '申請',
                        '完了' => '完了',
                        'キャンセル' => 'キャンセル',
                    ])
                    ->updateStateUsing(function ($record, $state) {
                        $record->exchanged_at = now();
                        $record->status = $state;
                        $record->save();
                        return $state;
                    })
                    ->disablePlaceholderSelection(),
                \Filament\Tables\Columns\BadgeColumn::make('status2')
                    ->formatStateUsing(function ($state, $record) {
                        ray($record);
                        $record->status2 = $record->status;
                        return $record->status;
                    })
                    ->colors([
                        'danger' => '申請',
                        'success' => '完了',
                        'secondary' => 'キャンセル',
                    ]),
//            Tables\Columns\TextColumn::make('status2')->label('状態')->formatStateUsing(function ($state, $record) {
//                return $record->status;
//            })->alignCenter(),
                Tables\Columns\TextColumn::make('customer.full_name')->label('顧客名')->alignCenter(),

                Tables\Columns\TextColumn::make('point')->label('交換ポイント')
                    ->formatStateUsing(function ($state, $record) {
                        return number_format($state);
                    })
                    ->alignRight(),

                Tables\Columns\TextColumn::make('requested_at')->label('依頼日')->alignCenter(),
                Tables\Columns\TextColumn::make('exchanged_at')->label('反映日')->alignCenter(),
            ]),
            \Filament\Tables\Columns\Layout\Panel::make([
                \Filament\Tables\Columns\Layout\Stack::make([
                    \Filament\Tables\Columns\TextColumn::make('customer.full_name'),
                    \Filament\Tables\Columns\TextColumn::make('customer.default_address.contact_email'),
                    \Filament\Tables\Columns\TextColumn::make('customer.default_address.contact_phone'),
                    \Filament\Tables\Columns\TextColumn::make('customer.default_address.postcode'),
                    \Filament\Tables\Columns\TextColumn::make('customer.default_address.state'),
                    \Filament\Tables\Columns\TextColumn::make('customer.default_address.city'),
                    \Filament\Tables\Columns\TextColumn::make('customer.default_address.line_one'),
                    \Filament\Tables\Columns\TextColumn::make('customer.default_address.line_two'),
                    \Filament\Tables\Columns\TextColumn::make('customer.default_address.line_three'),
                ]),
            ])->collapsible(),
        ];
    }

    protected function getTableActions(): array
    {
        return [

            Tables\Actions\Action::make('order-line')
                ->label('顧客詳細情報')
                ->url(
                    function ($record) {
                        return '/hub/customers/' . $record->customer_id;
                    }
                )->size('sm')
                ->icon('heroicon-o-link'),
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            //   ]),
        ];
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


//    protected function getTableContentFooter()
//    {
//        if(\Auth::user()->brand_id){
//            return null;
//        }
//        return view('adminhub::livewire.components.reports.table-footer', $this->data_list);
//    }

    protected function getTableFilters(): array
    {


//        //セレクトオプションを作成
//        $start = Carbon::create(2023, 4);//開始年月
//        $end = Carbon::now();
//        $dates = [];
//
//        while ($start->lte($end)) {
//            $dates[$start->format('Y-m')] = $start->format('Y年n月');
//            $start->addMonth();
//        }


        return [
            Filter::make('requested_at')->columnSpan('full')
                ->form([
                    Grid::make(5)
                        ->schema([
                            DatePicker::make('from')->displayFormat('Y年n月d日')->label('依頼日:抽出開始日')->columnSpan(1),
                            DatePicker::make('until')->displayFormat('Y年n月d日')->label('依頼日:抽出終了日')->columnSpan(1),
                            // ...
                        ]),


                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['from'],
                            fn(Builder $query, $date): Builder => $query->whereDate('requested_at', '>=', $data['from']),
                        )
                        ->when(
                            $data['until'],
                            fn(Builder $query, $date): Builder => $query->whereDate('requested_at', '<=', $data['until']),
                        );
                })
                ->indicateUsing(function (array $data): array {
                    $indicators = [];

                    if ($data['from'] ?? null) {
                        $indicators['from'] = '依頼日 抽出開始日 ' . Carbon::parse($data['from'])->format('Y年n月d日');
                    }

                    if ($data['until'] ?? null) {
                        $indicators['until'] = '依頼日 抽出終了日 ' . Carbon::parse($data['until'])->format('Y年n月d日');
                    }

                    return $indicators;
                }),
//            SelectFilter::make('lunar_orders.placed_at')
//                ->options($dates)
//                ->query(function (Builder $query, array $data) use($dates): Builder {
//
//                    if(isset( $this->tableFilters['lunar_orders'])){
//                        $data= $this->tableFilters['lunar_orders']['placed_at']['value'];
//                        ray( $this->tableFilters['lunar_orders'])->red()->label('data');
//                    }
//
//                    ray($dates)->red()->label('$dates');
//                   // ray($dates)->red()->label('$dates');
//                    if(empty($data)){
//                        $data = Carbon::now();
//                    //    $data=end($dates) ;
//                      //  $data=$dates['2023-04'];
//                    }
//                    ray($query)->red()->label('$query');
//                    $start = Carbon::create($data);
//                    $monthStart = $start->startOfMonth()->toDateString(); // get first day of the month
//                    $monthEnd = $start->endOfMonth()->toDateString(); // get last day of the month
//
//                    ray($monthStart)->red()->label('$monthStart');
//                    ray($monthEnd)->red()->label('$monthEnd');
//
//                    return $query
//                        ->when(
//                            $monthStart,
//                            fn (Builder $query, $date): Builder => $query->whereDate('lunar_orders.placed_at', '>=', $date),
//                        )
//                        ->when(
//                            $monthEnd,
//                            fn (Builder $query, $date): Builder => $query->whereDate('lunar_orders.placed_at', '<=', $date),
//                        );
//                })->default(function() use($dates){
//                    return array_key_last($dates);
//                })->label('年月'),


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
        return view('adminhub::livewire.components.points.index')
            ->layout('adminhub::layouts.app', [
                'title' => 'レポート',
            ]);
    }
}
