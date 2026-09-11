<?php

namespace App\View\Components;

use App\Models\Sector;
use App\Models\StateMaster;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ClientForm extends Component
{
    /**
     * Create a new component instance.
     */
    public $sector;
    public $stateList;
    public function __construct()
    {
        //
        $this->sector = (new Sector())->getSectorListOrm()->orderBy("id","ASC")->get();
        $this->stateList = (new StateMaster())->getStateOrm()->orderBy("state_name","ASC")->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.client-form');
    }
}
