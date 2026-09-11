<?php

namespace App\View\Components;

use App\Models\MachineMater;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PrintingScheduleForm extends Component
{
    /**
     * Create a new component instance.
     */
    public $printingMachine;
    public function __construct()
    {
        $this->printingMachine = MachineMater::where("lock_status",false)->where("is_printing",true)->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.printing-schedule-form');
    }
}
