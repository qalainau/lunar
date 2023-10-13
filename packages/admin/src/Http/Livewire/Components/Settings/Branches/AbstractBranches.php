<?php

namespace Lunar\Hub\Http\Livewire\Components\Settings\Branches;

use Illuminate\Support\Collection;
use Lunar\Facades\DB;
use Livewire\Component;
use Lunar\Hub\Http\Livewire\Traits\Notifies;
use Lunar\Hub\Models\Branch;

abstract class AbstractBranches extends Component
{
    use Notifies;

    /**
     * The staff model for the staff member we want to show.
     */
    public Branch $branch;


}
