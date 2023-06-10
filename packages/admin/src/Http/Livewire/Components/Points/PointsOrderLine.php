<?php

namespace Lunar\Hub\Http\Livewire\Components\Points;

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

class PointsOrderLine extends Component  implements Tables\Contracts\HasTable
{

    use Tables\Concerns\InteractsWithTable;


    public function mount($filter,$brand_id)
    {

        if(\Auth::user()->brand_id){
            $this->tableFilters['brand_id']['value']=\Auth::user()->brand_id;
        }
        else{
            $this->tableFilters['brand_id']['value']=$brand_id;
        }
        $this->tableFilters['lunar_orders']['placed_at']['value']=$filter;


        ray($this->tableFilters)->green();
//        $order_id = request()->route()->parameter('order_id');
//
//        $this->order=\Lunar\Models\Order::find( $order_id);
//        if($this->order->user_id !== auth()->user()->id){
//            // abort(403);
//            return redirect()->route('home');
//        }
    }

    protected function getTableQuery(): Builder
    {

        if(\Auth::user()->brand_id){
            return \App\Models\OrderLine::query()->with(['purchasable'])
                ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
                ->where('type', '=','physical')
                ->where('brand_id', '=',\Auth::user()->brand_id);
        }
         return \App\Models\OrderLine::query()->with(['purchasable'])
             ->join('lunar_orders', 'lunar_orders.id', '=', 'lunar_order_lines.order_id')
             ->where('type', '=','physical');
    }
    protected function getTableColumns(): array
    {



        return [

//            Tables\Columns\IconColumn::make('notification_icon')
//                ->options([
//            'heroicon-o-link',
//                ]),
            Tables\Columns\TextColumn::make('id')->label('販売元')->alignCenter(),
            Tables\Columns\TextColumn::make('purchasable.product.brand.name')->label('販売元')->alignCenter(),
            Tables\Columns\TextColumn::make('purchasable.product.brand.id')->label('販売dd元')->alignCenter(),

          //  Tables\Columns\TextColumn::make('purchasable.product.brand.id')->label('販売dd元')->alignCenter(),
            Tables\Columns\ImageColumn::make('sfafa')->label('画像')->getStateUsing(
                function ( $record) {
                 //  ray($record->product2)->red();
                    //ray($record->purchasable)->red();
                    if($record->type === 'physical') {
                        $product_variant = \Lunar\Models\ProductVariant::find($record->purchasable_id);
                        //$unit_price = $product_variant->getThumbnail();
                        return  $product_variant->getThumbnail()->original_url;
                    }
                    return '';
                }
            )->size(60)->circular(),
//            Tables\Columns\ImageColumn::make('purchasable.thumbnail')->url
//                function ( $record) {
//
//                    if ($record->type === 'physical'){
//                        ray($record->purchasable->thumbnail);
//                        //       $product_variant=\Lunar\Models\ProductVariant::find($record->purchasable_id);
//                        //   $product_variant=\Lunar\Models\ProductVariant::find($record->purchasable_id);
//                        //$unit_price = $product_variant->getThumbnail();
//                        return $record->purchasable->thumbnail;
//                    }
//                    return '';
//                //return $record->purchasable->thumbnail;
//            })->label('画像')->alignRight(),
            Tables\Columns\TextColumn::make('description')->label('商品名')->description(function ($record){
                return $record->option;
            }),
            Tables\Columns\TextColumn::make('unit_price')->formatStateUsing(function ($state, $record) {
                $unit_price=0;
                if(isset($record->unit_price->value)){
                    $unit_price = $record->unit_price->value;
                }
              //  $unit_price = $record->unit_price->value;
                return number_format($unit_price*1.1).'円';
                //return $original_total;
            })->label('税込単価')->alignRight(),
            Tables\Columns\TextColumn::make('quantity')->label('個数')->alignRight(),


            Tables\Columns\TextColumn::make('total')->formatStateUsing(function ($state, $record) {
                $original_total=0;
                if(isset($record->total->value)){
                    $original_total = $record->total->value;
                }
               // $original_total = $record->total->value;
                return number_format($original_total).'円';
                //return $original_total;
            })->label('税込合計')->alignRight(),


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
            Tables\Actions\Action::make('product_page')
                ->label('商品ページ')
                ->url(
                    function ($record){
                        if($record->type === 'physical') {
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
                    if(\Auth::user()->brand_id){
                        return $query->where('id',\Auth::user()->brand_id);
                    }
                        return $query;
                    }
                )
                ->default(function() {
                    if(isset($this->tableFilters['brand_id'])){
                        ray($this->tableFilters['brand_id'])->red()->label('brand_id');
                        return $this->tableFilters['brand_id']['value'];
                    }
                    return  null;
                }),

            SelectFilter::make('lunar_orders.placed_at')
                ->options($dates)
                ->query(function (Builder $query, array $data) use($dates): Builder {

                    if(isset( $this->tableFilters['lunar_orders'])){
                        $data= $this->tableFilters['lunar_orders']['placed_at']['value'];
                        ray( $this->tableFilters['lunar_orders'])->red()->label('data');
                    }

                    ray($dates)->red()->label('$dates');
                    // ray($dates)->red()->label('$dates');
                    if(empty($data)){
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

                    return $query
                        ->when(
                            $monthStart,
                            fn (Builder $query, $date): Builder => $query->whereDate('lunar_orders.placed_at', '>=', $date),
                        )
                        ->when(
                            $monthEnd,
                            fn (Builder $query, $date): Builder => $query->whereDate('lunar_orders.placed_at', '<=', $date),
                        );
                })->default(function() use($dates){
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
        return view('adminhub::livewire.components.points.order-line')
            ->layout('adminhub::layouts.app', [
                'title' => 'ポイント',
            ]);
    }
}
