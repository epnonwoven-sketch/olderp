<?php

namespace App\View\Components\Roll;

use App\Models\AutoDetail;
use App\Models\ClientDetailMaster;
use App\Models\RateTypeMaster;
use App\Models\TransporterDetail;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TransportModel extends Component
{
    /**
     * Create a new component instance.
     */
    public $autoList;
    public $transporterList;
    public $clientList;
    public $rateType;
    public function __construct()
    {
        $this->autoList =AutoDetail::where("lock_status",false)->orderBy("id","ASC")->get();
        $this->transporterList = TransporterDetail::where("lock_status",false)->orderBy("id","ASC")->get();
        $this->rateType = RateTypeMaster::all();
        $this->clientList = (new ClientDetailMaster())->getClientListOrm()->where("id","<>",1)->orderBy("client_name","ASC")->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.roll.transport-model');
    }
}
