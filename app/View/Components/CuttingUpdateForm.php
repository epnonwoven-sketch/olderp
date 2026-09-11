<?php

namespace App\View\Components;

use App\Models\CuttingMachine;
use App\Models\MachineMater;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CuttingUpdateForm extends Component
{
    /**
     * Create a new component instance.
     */
    public $cuttingMachineList;
    public function __construct()
    {
        $this->cuttingMachineList = MachineMater::where("lock_status",false)->where("is_cutting",true)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cutting-update-form');
    }
}
