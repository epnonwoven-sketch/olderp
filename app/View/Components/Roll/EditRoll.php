<?php

namespace App\View\Components\Roll;

use App\Models\ColorMaster;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EditRoll extends Component
{
    /**
     * Create a new component instance.
     */
    public $color;
    public function __construct()
    {
        $this->color= (new ColorMaster())->getColorListOrm()->orderBy("id")->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.roll.edit-roll');
    }
}
