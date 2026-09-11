<?php

namespace App\Http\Controllers;

use App\Models\BagPacking;
use App\Models\LoopStock;
use App\Models\RollDetail;
use App\Models\RollTransit;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Dashboard extends Controller
{
    protected $_M_LoopStock;
    protected $_M_RollDetail;
    protected $_M_RollTransit;
    protected $_M_BagPacking;
    function __construct()
    {
        $this->_M_LoopStock = new LoopStock();
        $this->_M_RollDetail = new RollDetail();
        $this->_M_RollTransit = new RollTransit();
        $this->_M_BagPacking = new BagPacking();
    }

    public function home(){
        return view("home");
    }

    public function loopStatus(Request $request){
        try{
            $data = $this->_M_LoopStock->getLoopColorOrm()->orderBy("id","ASC")->get();
            return responseMsgs(true,"",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function nearOrderDispatched(){
        
    }

    public function rollStatus(Request $request){
        $request->merge(["dashboardData"=>true]);
        $controller = App::makeWith(MasterController::class); 
        return $controller->rollShortageLimitList($request);
    }
}
