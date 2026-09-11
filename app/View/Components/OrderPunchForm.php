<?php

namespace App\View\Components;

use App\Models\BagTypeMaster;
use App\Models\ClientDetailMaster;
use App\Models\ColorMaster;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class OrderPunchForm extends Component
{
    /**
     * Create a new component instance.
     */
    public $clientList;
    public $bagType;
    public $color;
    public function __construct()
    {
        $this->clientList = (new ClientDetailMaster())->getClientListOrm()->get();
        $this->bagType = (new BagTypeMaster())->getBagListOrm()->get();
        $this->color = (new ColorMaster())->getColorListOrm()->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.order-punch-form');
    }
}
