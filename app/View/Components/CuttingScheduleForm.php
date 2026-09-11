<?php

namespace App\View\Components;

use App\Models\MachineMater;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CuttingScheduleForm extends Component
{
    /**
     * Create a new component instance.
     */
    public $cuttingMachine;
    public function __construct()
    {
        $this->cuttingMachine = MachineMater::where("lock_status",false)->where("is_cutting",true)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cutting-schedule-form');
    }
}
