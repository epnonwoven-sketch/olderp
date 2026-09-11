<?php

namespace App\View\Components;

use App\Models\BagType;
use App\Models\BagTypeMaster;
use App\Models\ClientDetail;
use App\Models\ClientDetailMaster;
use App\Models\ColorMaster;
use App\Models\RollColorMaster;
use App\Models\VendorDetail;
use App\Models\VendorDetailMaster;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RollForm extends Component
{
    /**
     * Create a new component instance.
     */
    public $vendorList;
    public $clientList;
    public $bagType;
    public $rollColor;
    public $color;
    public function __construct()
    {
        $this->vendorList = (new VendorDetailMaster())->getVenderListOrm()->get();
        $this->clientList = (new ClientDetailMaster() )->getClientListOrm()->get();
        $this->bagType = (new BagTypeMaster() )->getBagListOrm()->get();
        $this->rollColor = (new RollColorMaster())->getRollColorListOrm()->get();
        $this->color = (new ColorMaster())->getColorListOrm()->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.roll-form');
    }
}
