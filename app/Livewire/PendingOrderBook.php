<?php

namespace App\Livewire;

use App\Models\BagTypeMaster;
use App\Models\ColorMaster;
use App\Models\FareDetail;
use App\Models\GradeMaster;
use App\Models\OrderPunchDetail;
use App\Models\RateTypeMaster;
use App\Models\RollColorMaster;
use App\Models\RollDetail;
use App\Models\RollTransit;
use App\Models\StereoDetail;
use Livewire\Component;

class PendingOrderBook extends Component
{
    
    protected $listeners = ['refreshComponent'=>"refreshComponent"];

    public $pendingOrder;
    public $bagType;
    public $color;
    public $rollColor;
    public $grade;
    public $fare;
    public $stereo;
    public $rateType;
    public $gsm;
    public $altRollColor;
    public $altGsm;

    public function mount()
    {
        $this->pendingOrder = (new OrderPunchDetail())
            ->getPendingOrderOrm()
            ->orderBy("id", "ASC")
            ->get()
            ->map(function ($val) {
                $client = $val->getClient();
                $val->client_name = $client->client_name ?? '';
                $val->bag_type = $val->getBagType()->bag_type ?? '';
                $val->grade = $val->getGrade()->grade ?? '';
                $val->rate_type = $val->getRateType()->Rate_type ?? '';
                $val->fare_type = $val->getFare()->fare_type ?? '';
                $val->stereo = $val->getStereo()->stereo ?? '';
                $val->bag_printing_color = (json_decode($val->bag_printing_color,true));
                $val->balance_units = round($val->total_units -( $val->booked_units + $val->disbursed_units));
                return $val;
            })->sortBy("client_name");

        $this->bagType = (new BagTypeMaster())->getBagListOrm()->orderBy("id")->get();
        $this->color = (new ColorMaster())->getColorListOrm()->orderBy("id")->get();
        $this->rollColor = (new RollColorMaster())->getRollColorListOrm()->orderBy("id")->get();
        $this->grade = (new GradeMaster())->getGradeListOrm()->orderBy("id")->get();
        $this->fare = (new FareDetail())->getFareListOrm()->orderBy("id")->get();
        $this->stereo = (new StereoDetail())->getStereoListOrm()->orderBy("id")->get();
        $this->rateType = (new RateTypeMaster())->getRateTypeListOrm()->orderBy("id")->get();
        $rollStockGsm = (new RollDetail())->select('gsm')->distinct()->pluck('gsm');
        $rollTransitGsm = (new RollTransit())->select('gsm')->where("gsm",">",2)->distinct()->pluck('gsm');
        $this->gsm = $rollStockGsm->union($rollTransitGsm)->unique()->sort()->values();

        $this->altRollColor = $this->rollColor;
        $this->altGsm = $this->gsm;
    }

    
    public function refreshComponent()
    {
        // Optionally, reinitialize any logic here
        $this->mount();
        $this->render();
    }



    public function render()
    {
        return view('livewire.pending-order-book');
    }
}
