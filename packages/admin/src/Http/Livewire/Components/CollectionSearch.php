<?php

namespace Lunar\Hub\Http\Livewire\Components;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Models\Collection as ModelsCollection;

class CollectionSearch extends Component
{
    /**
     * Should the browser be visible?
     */
    public bool $showBrowser = false;

    /**
     * The search term.
     *
     * @var string
     */
    public $searchTerm = null;

    /**
     * Max results we want to show.
     *
     * @var int
     */
    public $maxResults = 50;

    /**
     * Any existing collections to exclude from selecting.
     */
    public Collection $existing;

    /**
     * The currently selected collections.
     */
    public array $selected = [];


    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            // 'searchTerm' => 'required|string|max:255',
        ];
    }

    /**
     * Return the selected collections.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSelectedModelsProperty()
    {
        return ModelsCollection::whereIn('id', $this->selected)->get();
    }

    /**
     * Return the existing collection ids.
     *
     * @return array
     */
    public function getExistingIdsProperty()
    {
        return $this->existing->pluck('id');
    }

    /**
     * Listener for when show browser is updated.
     *
     * @return void
     */
    public function updatedShowBrowser()
    {
        $this->selected = [];
        $this->searchTerm = null;
        //add by u1
        $this->getResultsProperty();


    }

    /**
     * Add the collection to the selected array.
     *
     * @param string|int $id
     * @return void
     */
    public function selectCollection($id)
    {
        $this->selected[] = $id;
    }

    /**
     * Remove a collection from the selected collections.
     *
     * @param string|int $id
     * @return void
     */
    public function removeCollection($id)
    {
        $index = collect($this->selected)->search($id);
        unset($this->selected[$index]);
    }








//    public function getResultsProperty()
//    {
//        if (! $this->searchTerm) {
//            return null;
//        }
//
//        return ModelsCollection::search($this->searchTerm)
//            ->query(function (Builder $query) {
//                $query->with([
//                    'group',
//                ]);
//            })->paginate($this->maxResults);
//    }
//


    /**
     * Returns the computed search results.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getResultsProperty()
    {

        //$this->maxResults=1000;
//$this->searchTerm
//        $p = ModelsCollection::search('肉')
//            ->query(function (Builder $query) {
//
//                $query->with([
//                    'group',
//                ]);
//                $query->where(
//                    'parent_id', '>', 0
//                );
//                ray($query->toSql());
//            })->paginate($this->maxResults);

//        ray($p);

        $p = \Lunar\Models\Collection::where('parent_id', '>', 0)->with('group')->paginate($this->maxResults);

        ray($p);
        return $p;

//        return ModelsCollection::search($this->searchTerm)
//            ->query(function (Builder $query) {
//                $query->with([
//                    'group',
//                ]);
//            })->paginate($this->maxResults);
    }

    public function triggerSelect()
    {
        $this->emit('collectionSearch.selected', $this->selected);

        $this->showBrowser = false;
    }

    /**
     * Render the livewire component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('adminhub::livewire.components.collection-search')
            ->layout('adminhub::layouts.base');
    }
}
