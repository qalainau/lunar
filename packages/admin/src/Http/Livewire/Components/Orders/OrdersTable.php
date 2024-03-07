<?php

namespace Lunar\Hub\Http\Livewire\Components\Orders;

use Illuminate\Support\Collection;
use Lunar\Facades\DB;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Hub\Models\SavedSearch;
use Lunar\Hub\Tables\Builders\OrdersTableBuilder;
use Lunar\LivewireTables\Components\Actions\Action;
use Lunar\LivewireTables\Components\Actions\BulkAction;
use Lunar\LivewireTables\Components\Filters\DateFilter;
use Lunar\LivewireTables\Components\Filters\SelectFilter;
use Lunar\LivewireTables\Components\Table;
use Lunar\Models\Order;
use Lunar\Models\Tag;

class OrdersTable extends Table
{
    use Notifies;

    /**
     * {@inheritDoc}
     */
    protected $tableBuilderBinding = OrdersTableBuilder::class;

    /**
     * {@inheritDoc}
     */
    public bool $searchable = true;

    /**
     * {@inheritDoc}
     */
    public bool $canSaveSearches = true;

    /**
     * {@inheritDoc}
     */
    public ?string $poll = null;

    /**
     * The customer ID to hard filter results by.
     *
     * @var string|int
     */
    public $customerId = null;

    /**
     * {@inheritDoc}
     */
    protected $listeners = [
        'saveSearch' => 'handleSaveSearch',
    ];

    /**
     * {@inheritDoc}
     */
    public function build()
    {
        $this->filters['placed_at'] = $this->filters['placed_at'] ?? null;
        $this->filters['vendor'] = $this->filters['vendor'] ?? 999999;

        $this->tableBuilder->addFilter(
            SelectFilter::make('status')->options(function () {
                $statuses = collect(
                    config('lunar.orders.statuses'),
                    []
                )->mapWithKeys(fn($status, $key) => [$key => $status['label']]);
                return collect([
                    null => 'All Statuses',
                ])->merge($statuses);
            })->query(function ($filters, $query) {
                $value = $filters->get('status');

                if ($value) {
                    $query->whereStatus($value);
                }
            })
        );

//        $this->tableBuilder->addFilter(
//            SelectFilter::make('tags')->options(function () {
//                $tagTable = (new Tag)->getTable();
//
//                $tags = DB::connection(config('lunar.database.connection'))
//                    ->table(config('lunar.database.table_prefix') . 'taggables')
//                    ->join($tagTable, 'tag_id', '=', "{$tagTable}.id")
//                    ->whereTaggableType(Order::class)
//                    ->distinct()
//                    ->pluck('value')
//                    ->map(function ($value) {
//                        return [
//                            'value' => $value,
//                            'label' => $value,
//                        ];
//                    });
//
//                return collect([
//                    null => 'None',
//                ])->merge($tags);
//            })->query(function ($filters, $query) {
//                $value = $filters->get('tags');
//
//                if ($value) {
//                    $query->whereHas('tags', function ($query) use ($value) {
//                        $query->whereValue($value);
//                    });
//                }
//            })
//        );

//        $this->tableBuilder->addFilter(
//            SelectFilter::make('new_returning')->options(function () {
//                return collect([
//                    null => 'Both',
//                    'new' => 'New',
//                    'returning' => 'Returning',
//                ]);
//            })->query(function ($filters, $query) {
//                $value = $filters->get('new_returning');
//
//                if ($value) {
//                    $query->whereNewCustomer(
//                        $value == 'new'
//                    );
//                }
//            })
//        );

        $this->tableBuilder->addFilter(
            SelectFilter::make('vendor')
                ->heading('販売元')
                ->options(function () {
                    if (\Auth::user()->brand_id) {
                        $vendors = \Lunar\Models\Brand::where('id', \Auth::user()->brand_id)->get()->pluck('name', 'id');
                    } elseif (\Auth::user()->is_carrier) {
                        $vendors = \Lunar\Models\Brand::where('carrier_id', \Auth::user()->id)->get()->pluck('name', 'id');
                    } else {
                        $vendors = \Lunar\Models\Brand::all()->pluck('name', 'id');
                    }


                    ray($vendors);
                    // $vendors->push('全て');
                    $vendors->put(999999, "全て");
                    $vendors->sortBy('id');
                    // Convert the collection to an array
                    $array = $vendors->toArray();
// Sort the array by keys
                    krsort($array);

// Convert back to a collection if needed
                    $vendors = collect($array);
                    ray($vendors);
                    return collect($vendors);

//                    return collect([
//                        null => '全て',
//                    ])->merge($vendors);
                })->query(function ($filters, $query) {
                    $value = $filters->get('vendor');
                    if ($value && $value != 999999) {
                        $query->where(
                            'brand_id',
                            $value
                        );
                    }
                    if (\Auth::user()->brand_id) {
                        $vendors = \Lunar\Models\Brand::where('id', \Auth::user()->id)->get();
                        $brand_ids = [];
                        foreach ($vendors as $vendor) {
                            $brand_ids[] = $vendor->id;
                        }
                        ray($brand_ids)->green();
                        $query->whereIn('brand_id', $brand_ids);
                    }
                    if (\Auth::user()->is_carrier) {
                        $vendors = \Lunar\Models\Brand::where('carrier_id', \Auth::user()->id)->get();
                        $brand_ids = [];
                        foreach ($vendors as $vendor) {
                            $brand_ids[] = $vendor->id;
                        }
                        ray($brand_ids)->green();
                        $query->whereIn('brand_id', $brand_ids);
                    }
                })
        );


        $this->tableBuilder->addFilter(
            DateFilter::make('placed_at')
                ->heading('購入日')
                ->query(function ($filters, $query) {
                    $value = $filters->get('placed_at');
                    ray($value)->green();
                    if (!$value) {
                        return $query;
                    }

                    $parts = explode(' to ', $value);

//                    if (empty($parts[1])) {
//                        return $query;
//                    }

                    if (empty($parts[1])) {
                        $parts[1] = $parts[0];
                    }
                    ray($parts)->red();
                    $query->whereBetween('placed_at', [
                        $parts[0] . ' 00:00:00',
                        $parts[1] . ' 23:59:59',
                    ]);
                })
        );


        $this->tableBuilder->addAction(
            Action::make('view')->label('View Order')->url(function ($record) {
                return route('hub.orders.show', $record->id);
            })
        );

        $this->tableBuilder->addBulkAction(
            BulkAction::make('update_status')
                ->label('Update Status')
                ->livewire('hub.components.tables.actions.update-status')
        );

    }

    /**
     * Remove a saved search record.
     *
     * @param int $id
     * @return void
     */
    public function deleteSavedSearch($id)
    {
        SavedSearch::destroy($id);

        $this->resetSavedSearch();

        $this->notify(
            __('adminhub::notifications.saved_searches.deleted')
        );
    }

    /**
     * Save a search.
     *
     * @return void
     */
    public function saveSearch()
    {
        $this->validateOnly('savedSearchName', [
            'savedSearchName' => 'required',
        ]);

        auth()->getUser()->savedSearches()->create([
            'name' => $this->savedSearchName,
            'term' => $this->query,
            'component' => $this->getName(),
            'filters' => $this->filters,
        ]);

        $this->notify('Search saved');

        $this->savedSearchName = null;

        $this->emit('savedSearch');
    }

    /**
     * Return the saved searches available to the table.
     */
    public function getSavedSearchesProperty(): Collection
    {
        return auth()->getUser()->savedSearches()->whereComponent(
            $this->getName()
        )->get()->map(function ($savedSearch) {
            return [
                'key' => $savedSearch->id,
                'label' => $savedSearch->name,
                'filters' => $savedSearch->filters,
                'query' => $savedSearch->term,
            ];
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        $filters = $this->filters;
        $query = $this->query;

        if ($this->customerId) {
            return Order::whereCustomerId($this->customerId)
                ->paginate($this->perPage);
        }
        ray($filters)->red();
        // ray($query)->red();
        return $this->tableBuilder
            ->searchTerm($query)
            ->queryStringFilters($filters)
            ->perPage($this->perPage)
            ->sort(
                $this->sortField ?: 'placed_at',
                $this->sortDir ?: 'desc',
            )->getData();
    }
}
