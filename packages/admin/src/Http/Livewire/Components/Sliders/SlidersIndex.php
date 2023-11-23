<?php

namespace Lunar\Hub\Http\Livewire\Components\Sliders;

use Closure;
use DB;
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

class SlidersIndex extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;


    public function mount()
    {


    }

    protected function getTableQuery()
    {
        return \App\Models\Slider::query()->orderBy('id', 'ASC');
    }

    protected function getTableColumns(): array
    {

        return [

            Tables\Columns\TextColumn::make('id')->label('表示順')->alignCenter(),
            Tables\Columns\IconColumn::make('is_show')->label('表示状態')
                ->alignCenter()
                ->options([
                    'heroicon-o-ban' => fn($state, $record): bool => $state === 0,
                    'heroicon-o-check-circle' => fn($state): bool => $state === 1,
                ])
                ->colors([
                    'secondary' => 0,
                    'success' => 1,
                ]),
            Tables\Columns\ImageColumn::make('image')->label('画像'),
//            Tables\Columns\TextColumn::make('brand_id')->formatStateUsing(function ($state, $record) {
//                return \Lunar\Models\Brand::find($record->brand_id)->name;
//            })->label('販売元')->alignRight(),
//
//            Tables\Columns\TextColumn::make('count')->label('販売回数')->alignRight(),
//            Tables\Columns\TextColumn::make('quantity')->label('個数')->alignRight(),
//
//
//            Tables\Columns\TextColumn::make('total.value')
////                ->getStateUsing(function ( $record ) {
////                    $original_total=0;
////                    $record->total= $record->total->value;
////
////                   // return number_format($original_total).'円';
////                })
//                ->formatStateUsing(function ($state, $record) {
//                    $original_total = 0;
//                    if (isset($record->total->value)) {
//                        $original_total = $record->total->value;
//                        $record->total = $record->total->value;
//                    }
//                    return number_format($original_total) . '円';
//                })->label('税込合計')->alignRight(),
//
//            Tables\Columns\TextColumn::make('commission')->formatStateUsing(function ($state, $record) {
//                $commission = 0;
//                if (isset($record->total->value)) {
//                    $commission = round($record->total->value / 100 * 7);
//                    $record->commission = $commission;
//                    $record->payment = $record->total->value - $commission;
//                }
//                return number_format($commission) . '円';
//            })->label('手数料(7%)')->alignRight(),
//
//            Tables\Columns\TextColumn::make('payment')->formatStateUsing(function ($state, $record) {
//                return number_format($state) . '円';
//            })->label('販売元手取分')->alignRight(),


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
            Tables\Actions\Action::make('edit')
                ->label('編集')
                ->url(
                    function ($record) {
                        return '/hub/sliders/edit/' . $record->id;

                    }
                )->size('sm'),
            //  ->icon('heroicon-o-link'),
//                Tables\Actions\ViewAction::make(),
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            //   ]),
        ];
    }


//    protected function isTablePaginationEnabled(): bool
//    {
//        return true;
//    }
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
//        return view('adminhub::livewire.components.reports.table-footer', $this->data_list);
//    }

//    protected function getTableFilters(): array
//    {
//
//
//    }

//    protected function getTableFiltersLayout(): ?string
//    {
//        return Layout::AboveContent;
//    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.sliders.index')
            ->layout('adminhub::layouts.app', [
                'title' => 'レポート',
            ]);
    }
}
