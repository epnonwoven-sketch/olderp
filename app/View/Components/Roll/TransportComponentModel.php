<?php

namespace App\View\Components\Roll;

use App\Models\AutoDetail;
use App\Models\ClientDetailMaster;
use App\Models\RateTypeMaster;
use App\Models\TransporterDetail;
use App\Models\VendorDetailMaster;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TransportComponentModel extends Component
{
    /**
     * Create a new component instance.
     */
    /**
     * Create a new component instance.
     */
    public $autoList;
    public $transporterList;
    public $clientList;
    public $rateType;
    public $vendorList;
    public $purpose = [
        "Sale","Lamination","Cutting","Printing"
    ];
    public function __construct()
    {
        $this->autoList =AutoDetail::where("lock_status",false)->orderBy("id","ASC")->get();
        $this->transporterList = TransporterDetail::where("lock_status",false)->where("is_bus",false)->orderBy("id","ASC")->get();
        $this->rateType = RateTypeMaster::all();
        $this->clientList = (new ClientDetailMaster())->getClientListOrm()->where("id","<>",1)->orderBy("client_name","ASC")->get();
        $this->vendorList = (new VendorDetailMaster())->getVenderListOrm()->orderBy("vendor_name","ASC")->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.roll.transport-component-model');
    }
}
