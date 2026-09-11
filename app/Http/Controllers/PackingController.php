<?php

namespace App\Http\Controllers;

use App\Exceptions\MyException;
use App\Models\AutoDetail;
use App\Models\BagPacking;
use App\Models\BagPackingTransport;
use App\Models\BagPackingTransportDetail;
use App\Models\BagTypeMaster;
use App\Models\ChalanDtl;
use App\Models\ClientDetailMaster;
use App\Models\GarbageAcceptRegister;
use App\Models\GarbageEntry;
use App\Models\OrderPunchDetail;
use App\Models\RateTypeMaster;
use App\Models\RollDetail;
use App\Models\Sector;
use App\Models\TransporterDetail;
use App\Traits\Formula;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
// use Barryvdh\Snappy\Facades\SnappyPdf as PDF;


class PackingController extends Controller
{
    use Formula;
    private $_M_RollDetail;
    private $_M_ClientDetails;
    private $_M_BagType;

    private $_M_BagPacking;
    private $_M_PackTransport;
    private $_M_TransportDetail;
    protected $_M_OrderPunchDetail;
    protected $_M_Auto;
    protected $_M_Transporter;
    protected $_M_RateType;
    protected $_M_ChalanDtl;
    protected $_M_GarbageEntry;
    protected $_M_GarbageAcceptRegister;
    protected $_M_Sector;
    function __construct()
    {
        $this->_M_RollDetail = new RollDetail();
        $this->_M_ClientDetails = new ClientDetailMaster();
        $this->_M_BagType = new BagTypeMaster();
        $this->_M_BagPacking = new BagPacking();
        $this->_M_PackTransport  = new BagPackingTransport();
        $this->_M_TransportDetail = new BagPackingTransportDetail();
        $this->_M_OrderPunchDetail = new OrderPunchDetail();
        $this->_M_Auto  =  new AutoDetail();
        $this->_M_Transporter = new TransporterDetail();
        $this->_M_RateType = new RateTypeMaster();
        $this->_M_ChalanDtl = new ChalanDtl();
        $this->_M_GarbageEntry = new GarbageEntry();
        $this->_M_GarbageAcceptRegister = new GarbageAcceptRegister();
        $this->_M_Sector = new Sector();
    }

    public function packingEnter(Request $request){
        $roll = $this->_M_RollDetail
                    ->where("is_cut",true)
                    ->where("lock_status",false)
                    ->where("is_delivered",false)
                    ->get();
        $clientId = $roll->unique("client_detail_id")->pluck("client_detail_id");
        $data["clientList"] = $this->_M_ClientDetails->whereIn("id",$clientId)->get();
        $bagId =  $roll->unique("bag_type_id")->pluck("bag_type_id");
        $data["bagList"] = $this->_M_BagType->whereIn("id",$bagId)->get();
        return view("Packing/entry",$data);
    }

    public function WIPVerification(Request $request){
        if($request->ajax())
        {
            DB::enableQueryLog();
            $data = $this->_M_OrderPunchDetail
            ->select(DB::raw("order_punch_details.*,client_detail_masters.client_name,bag_type_masters.bag_type,
                            COALESCE(roll_weight,0) as roll_weight,
                            COALESCE(total_garbage,0) as total_garbage,
                            COALESCE(packing_weight,0) as packing_weight,
                            COALESCE(packing_bag_pieces,0) as packing_bag_pieces,
                            COALESCE(bora_weight,0) as bora_weight,
                            roll.roll_ids
                            ")
            )
            ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
            ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")
            ->join(DB::raw("
                (
                    select sum(CASE WHEN is_printed=true then weight_after_print ELSE net_weight END ) as roll_weight,order_id,
                        string_agg(roll_details.id::text,',') as roll_ids
                    from roll_details
                    join order_roll_bag_types on order_roll_bag_types.roll_id = roll_details.id
                    where order_roll_bag_types.lock_status = false and roll_details.is_cut = true and roll_details.is_roll_sell=false
                    group by order_id
                ) as roll
            "),"roll.order_id","order_punch_details.id")
            ->leftJoin(DB::raw("
                (
                    select sum(total_qtr) as total_garbage,order_id
                    from garbage_accept_registers
                    join order_roll_bag_types on order_roll_bag_types.roll_id = garbage_accept_registers.roll_id
                    where order_roll_bag_types.lock_status = false and garbage_accept_registers.lock_status = false
                    group by order_id
                ) as garbage
            "),"garbage.order_id","order_punch_details.id")
            ->leftJoin(DB::raw("
                (
                    select sum(packing_weight) as packing_weight, sum(packing_bag_pieces)as packing_bag_pieces,
                        sum(bora_weight)as bora_weight,order_id
                    from bag_packings
                    where lock_status = false
                    group by order_id
                ) As packing
            "),"packing.order_id","order_punch_details.id")
            ->where("order_punch_details.is_delivered",false)
            ->where("order_punch_details.is_wip_disbursed",false)
            ->where("order_punch_details.lock_status",false)
            ->where("order_punch_details.is_draft",false)
            // ->where("order_punch_details.id",261)
            ->orderBy("order_punch_details.id")
            ->get()
            ->map(function($val){   
                $val->bora_weight_in_kg = $val->bora_weight/1000;         
                $gsm_json = $val->bag_gsm;
                $val->alt_bag_gsm = collect(json_decode($val->alt_bag_gsm,true))->implode(",");
                $val->alt_bag_color = collect(json_decode($val->alt_bag_color,true))->implode(",");
                $val->packing_date = $val->packing_date ? Carbon::parse($val->packing_date)->format("d-m-Y") : "";
                $val->bag_printing_color = collect(json_decode($val->bag_printing_color,true))->implode(",") ;
                $val->bag_color = collect(json_decode($val->bag_color,true))->implode(",") ;
                $val->bag_gsm = collect(json_decode($val->bag_gsm,true))->implode(",") ;
                $val->bag_gsm_json = collect(json_decode($val->bag_gsm_json,true))->implode(",");
                $val->bag_size = (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                
                $val->loop_weight = 0;
                $rolls = $this->_M_RollDetail->whereIn("id",explode(",",$val->roll_ids))->get();
                $totalPieces =0;
                foreach($rolls as $roll){                    
                    $bag = $this->_M_BagType->find($roll->bag_type_id);
                    $formula = $bag->roll_find;
                    $formula2 = $bag->roll_find_as_weight;

                    $newRequest = new Request();
                    $newRequest->merge(
                        [
                        "bookingBagUnits" => $roll->bag_unit,
                        "formula" => $formula,
                        "length" => $roll->length,
                        "netWeight"=>$roll->net_weight,
                        "size"=>$roll->size,
                        "gsm"=>$roll->gsm,

                        "bagL"=>$roll->l,
                        "bagW"=>$roll->w,
                        "bagG"=>$roll->g
                        ]
                    );
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula" => $formula2,
                    ]);

                    $result = $this->calculatePossibleProduction($newRequest);
                    $result2 = $this->calculatePossibleProduction($newRequest2);
                    $totalPieces += ((($result["result"]??0)+($result2["result"]??0))/2);
                }
                $oneKg = $totalPieces/$val->roll_weight;
                $garbagePec = $oneKg * $val->total_garbage;
                $totalProductPieces = round($totalPieces - $garbagePec);                
                $val->total_pieces =  $totalProductPieces ;
                $val->loop_weight = 0;
                if(in_array($val->bag_type_id,[2,4,5])){
                    $val->loop_weight = (($totalProductPieces*3.4)/1000); # convert it in kg
                }
                $uCuteGarbage = 0;
                if($val->bag_type_id==3){
                    $uCuteGarbage = (($val->roll_weight - $val->total_garbage)*0.1);
                }
                $val->u_cute_garbage = $uCuteGarbage;
                $val->balance = roundFigure($val->roll_weight + $val->loop_weight - $val->packing_weight + $val->bora_weight_in_kg - $val->total_garbage -$uCuteGarbage);
                $val->balance_in_pieces = 0;
                if($val->units!="Kg"){
                    $val->balance_in_pieces = $val->total_pieces - $val->packing_bag_pieces;
                }
                $initRollWight = ($val->roll_weight + $val->loop_weight - $val->total_garbage -$uCuteGarbage);
                $val->int_balance =  (float)roundFigure(($val->balance / ($initRollWight ? $initRollWight : 1)) * 100 );
                $val->balance_prc =  $val->int_balance." %";
                $gsm = collect(json_decode($gsm_json,true));
                $rs = Config::get("customConfig.BagTypeIdealWeightFormula.".$val->bag_type_id)["RS"]??"";                
                $val->formula_ideal_weight = $this->_M_BagType->find($val->bag_type_id)->weight_of_bag_per_piece;                
                $val->weight_per_bag = ($val->roll_weight + $val->loop_weight - $val->total_garbage - $uCuteGarbage )/$val->total_pieces;
                
                return $val;

            });
            if($request->dividend){
                $data = $data->whereBetween("int_balance",[2.00,10.00]);
            }
            // ->filter(function ($val) { 
            //     return $val->balance > 0 || $val->balance_in_pieces > 0;
            // });
            $summary=[
                "totalWeight"=>roundFigure($data->sum("balance")),
                "totalPieces"=>roundFigure($data->sum("balance_in_pieces")),
            ];
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('action', function ($val) {                    
                    $button = "";                    
                    // $button='<button class="btn btn-sm btn-info" onClick="openCuttingModel('.$val->id.')" >Update Cutting</button>';
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->with($summary)
                ->make(true);
            return $list;

        }
        return view("Packing/wip");
    }

    public function getPackingSerialNo(Request $request){
        try{
            $orderDate = Carbon::parse($request->packing_date);
            $rolNo = $orderDate->clone()->format("d/m/y")."-";
            $sl = BagPacking::where("packing_date",$orderDate->clone()->format('Y-m-d'))->count("id")+1;
            $slNo ="";
            while(true){   
                $slNo = str_pad((string)$sl,2,"0",STR_PAD_LEFT);                       
                $test = BagPacking::where("packing_no",$rolNo.$slNo)->count();
                if((!$test)){                    
                    $rolNo.=$slNo;
                    break;
                }
                $sl=($sl+1);
            }
            $data["sl"]=$sl+(collect($request->sl_nos)->count());
            return responseMsg(true,"sl",$data);
        }catch(MyException $e){
            return responseMsg(false,$e->getMessage(),$e->getMessage());
        }catch(Exception $e){
            return responseMsg(false,"server Error","");
        }
    }

    public function deleteWIP(Request $request){
        try{
            $order = $this->_M_OrderPunchDetail->find($request->orderId);
            $cuttingUpdate = ["is_cut"=>false,"cutting_date"=>null,"weight_after_cutting"=>null,"cutting_machine_id"=>null];
            $testBag = $this->_M_BagPacking->where("order_id",$order->id)->where("lock_status",false)->get();
            if($testBag->count()){
                throw new MyException("All Bag Are Note Delete kindly Delete it first. Bag No are ".$testBag->pluck("packing_no")->implode(","));
            }
            $garbageEnter = $this->_M_GarbageEntry->where("order_id",$order->id)->where('lock_status',false)->first();
            if(!$garbageEnter){
                $garbageEnter = $this->_M_GarbageEntry->where("client_id",$order->client_detail_id)->whereNotNull("wip_disbursed_in_kg")->where('lock_status',false)->first();
            }
            DB::beginTransaction();
            if($garbageEnter){
                $garbageEnter->lock_status = true;
                $garbageEnter->update();
                $garbageRoll = $this->_M_GarbageAcceptRegister->where("garbage_entry_id",$garbageEnter->id)->get();
                foreach($garbageRoll as $gr){
                    $gr->lock_status =true;
                    $gr->update();
                }
            }
            foreach(explode(",",$request->roll_ids) as $id){
                $roll = $this->_M_RollDetail->find($id);
                foreach($cuttingUpdate as $key => $val){
                    $roll->$key = $val;
                }
                $roll->update();
            }
            DB::commit();
            return responseMsg(true,"WIP Deleted","");
        }catch(MyException $e){
            DB::rollBack();
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsg(false,"Server Error!!!",'');
        }
    }

    public function disburseOrder(Request $request){
        try{
            $order = $this->_M_OrderPunchDetail->find($request->id);
            $order->is_wip_disbursed = true;
            $order->wip_disbursed_units =$request->balance;
            $order->wip_disbursed_pieces = $request->balance_pieces ? $request->balance_pieces : null ;
            $order->wip_disbursed_by = Auth::user()->id??null;
            $order->wip_disbursed_date= Carbon::now();
            $rolls = $this->_M_RollDetail->whereIn("id",explode(",",$request->roll_ids))->get()->map(function($item){
                $item->weight = $item->weight_after_print ? $item->weight_after_print : $item->net_weight;
                return $item;
            });
            $garbageEnter = $this->_M_GarbageEntry->where("order_id",$order->id)->where('lock_status',false)->first();
            if(!$garbageEnter){
                $garbageEnter = $this->_M_GarbageEntry->where("client_id",$order->client_detail_id)->whereNull("wip_disbursed_in_kg")->where('lock_status',false)->first();
            }
            if($garbageEnter){
                if(!$garbageEnter->order_id){
                    $garbageEnter->order_id = $order->id;
                }
                $garbageEnter->wip_disbursed_in_kg = $request->balance;
                $garbageEnter->wip_disbursed = $request->wip_disbursed_pieces ? $request->wip_disbursed_pieces : null ;
                $garbagePercent = ((($garbageEnter->garbage + $garbageEnter->wip_disbursed_in_kg) / $rolls->sum("weight"))*100);
                if(!is_between($garbagePercent,-2,2) && $garbageEnter->is_verify){
                    $garbageEnter->is_verify = false;
                }
            }
            DB::beginTransaction();
            $order->update();
            if($garbageEnter){
                $garbageEnter->update();
            }
            DB::commit();
            return responseMsgs(true,"Data Update","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),'');
        }
    }

    public function packingEnterWipAdd(Request $request){
        try{
            $rules = [
                "roll"=>"required|array",
                "roll.*.id"=>"required|exists:".$this->_M_OrderPunchDetail->getTable().",id",
                "roll.*.weight"=>"required",
                "roll.*.pieces"=> [                    
                    function ($attribute, $value, $fail) use($request){
                        $key = explode(".",$attribute)[1];
                        $roll = $this->_M_OrderPunchDetail->find($request->roll[$key]["id"]);
                        if($roll && $roll->units=="Piece" && (!$value))
                        {
                            $fail('The '.$attribute.' id required');
                        }
    
                    },
                ],
            ];
            
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $user = Auth()->user();
            DB::beginTransaction();
            foreach(collect($request->roll)->sortBy("sl_no") as $val){
                $newRequest = new Request();
                $orderDate = Carbon::parse($request->packing_date);
                $rolNo = $orderDate->clone()->format("d/m/y")."-";
                $sl = $val["sl_no"];
                $slNo =str_pad((string)$sl,2,"0",STR_PAD_LEFT);
                $rolNo.=$slNo;
                $test = BagPacking::where("packing_no",$rolNo)->count();
                if(($test)){ 
                    throw new Exception("Bora No ".$rolNo." Already Exist Please Enter Next");
                }
                $newRequest->merge([
                    "packing_no"=>$rolNo,
                    "packing_weight"=>$val["weight"],
                    "packing_bag_pieces"=>$val["pieces"]??null,
                    "order_id"=>$val["id"],
                    "user_id"=>$user->id,
                    "packing_date"=>Carbon::parse($request->packing_date)->format("Y-m-d"),
                    "bora_weight"=>Config::get("customConfig.boraWeightInGram"),
                ]);

                $this->_M_BagPacking->store($newRequest);
            }
            DB::commit();
            return responseMsgs(true,"Bag Entry Successful","");
            
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function searchRoll(Request $request){
        try{
            $roll=$this->_M_RollDetail->select("roll_details.*","bag_type_masters.bag_type")
                    ->join("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.is_cut",true)
                    ->where("roll_details.is_delivered",false)
                    ->where("roll_details.client_detail_id",$request->clientId);
            if($request->l){
                $roll->where("roll_details.l",$request->l);
            }
            if($request->w){
                $roll->where("roll_details.w",$request->w);
            }
            if($request->bag_type_id){
                $roll->where("roll_details.bag_type_id",$request->bag_type_id);
            }
            $roll = $roll->get();
            return responseMsgs(true,"data Fetch",$roll);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function packingEnterAdd(Request $request){        
        try{
            $rules = [
                "roll"=>"required|array",
                "roll.*.id"=>"required|exists:".$this->_M_RollDetail->getTable().",id",
                "roll.*.weight"=>"required",
                "roll.*.pieces"=> [                    
                    function ($attribute, $value, $fail) use($request){
                        $key = explode(".",$attribute)[1];
                        $roll = $this->_M_RollDetail->find($request->roll[$key]["id"]);
                        if($roll && $roll->bag_unit=="Piece" && (!$value))
                        {
                            $fail('The '.$attribute.' id required');
                        }
    
                    },
                ],
            ];
            
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $user = Auth()->user();
            DB::beginTransaction();
            foreach($request->roll as $val){
                $newRequest = new Request();
                $newRequest->merge([
                    "packing_weight"=>$val["weight"],
                    "packing_bag_pieces"=>$val["pieces"]??null,
                    "roll_id"=>$val["id"],
                    "user_id"=>$user->id,
                ]);

                $this->_M_BagPacking->store($newRequest);
            }
            DB::commit();
            return responseMsgs(true,"Bag Entry Successful","");
            
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function bagStock(Request $request){
        $user = Auth()->user();
        $user_type = $user->user_type;
        if($request->ajax()){
            $rateType = $this->_M_RateType->all();
            $data = $this->_M_BagPacking->select("order_punch_details.*","bag_packings.*",
                        "bag_type_masters.bag_type","client_detail_masters.client_name","client_detail_masters.city_id",
                        "client_detail_masters.state_id","client_detail_masters.has_address_two"
                    )
                    ->join("order_punch_details","order_punch_details.id","bag_packings.order_id")
                    ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
                    ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")
                    ->where("bag_packings.packing_status",1)
                    ->where("bag_packings.lock_status",false)
                    ->orderBy("bag_packings.order_id","DESC")
                    ->orderBy("bag_packings.id","DESC");
            // $data = $data->get();
            $summary=[
                "totalWeight"=>roundFigure($data->sum("packing_weight")),
            ];
            $list = DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('packing_date', function ($val) { 
                    return $val->packing_date ? Carbon::parse($val->packing_date)->format("d-m-Y") : "";
                })
                ->addColumn("is_local_order",function($val){
                    return in_array($val->city_id,Config::get("customConfig.localCityIds"))?true:false;
                })
                ->addColumn("rate_type",function($val)use($rateType){
                    return $rateType->where("id",$val->rate_type_id)->first()->rate_type??"";
                })
                ->addColumn('bag_color', function ($val) { 
                    return collect(json_decode($val->bag_color,true))->implode(",") ;
                })
                ->addColumn('bag_size', function ($val) { 
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn('bag_gsm', function ($val) { 
                    return collect(json_decode($val->bag_gsm,true))->implode(",");
                })
                ->addColumn('action', function ($val) { 
                    $button="";
                    if((!$val->is_wip_disbursed) && (!$val->is_delivered) ){
                        $button.='<button class="btn btn-sm btn-primary" onClick="editBag('.$val->id.')" >E</button>';
                        $button.='<button class="btn btn-sm btn-danger" onClick="showConfirmDialog('."'Are you sure you want to delete this item?', function() { deleteBag($val->id); })".'" >D</button>';
                    }                     
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->with($summary)
                ->make(true);
            return $list;

        }
        $data["autoList"] =$this->_M_Auto->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["rateType"] = $this->_M_RateType->all();
        $data["clientList"] = $this->_M_ClientDetails->getClientListOrm()->where("id","<>",1)->orderBy("client_name","ASC")->get();
        return view("Packing/stock",$data);
    }

    public function bagDtl($id){
        try{
            $data = $this->_M_BagPacking->find($id);
            DB::enableQueryLog();
            $data->units = $data->getOrderDtl()->units??"";
            return responseMsg(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsg(false,$e->getMessage(),"");
        }
    }

    public function editBag(Request $request){
        try{
            $rules=[
                "id"=>"required|exists:".$this->_M_BagPacking->getTable().",id",
                "packing_weight"=>"required|numeric",
                "packing_bag_pieces"=>"nullable|numeric",
                "client_id"=>"nullable"
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $bag = $this->_M_BagPacking->find($request->id);
            $order = $bag->getOrderDtl();
            if(!$request->client_id){
                $request->merge(["client_id"=>$order->client_detail_id]);
            }
            $bagStatus = flipConstants(collect(Config::get("customConfig.bagStatus")));
            if($bag->packing_status==$bagStatus["dispatched"]){
                throw new MyException("Bag is Dispatch");
            }
            if($order && $order->is_wip_disbursed){
                throw new MyException("can't Delete the roll because order is verified");
            }
            $bag->packing_weight = $request->packing_weight;
            $bag->packing_bag_pieces = $request->packing_bag_pieces;
            if(!$bag->packing_no){
                $bag->packing_no = $request->packing_no ;
            }
            // if($order->bag_printing_color && $order->client_detail_id != $request->client_id){
            //     throw new MyException("This Bag is Printed Show Client Not Change.");
            // }
            // if($order->client_detail_id != $request->client_id){
            //     $bag->client_id = $request->client_id;
            //     $bag->is_bag_assign = true;
            // }else{
            //     $bag->client_id = null;
            //     $bag->is_bag_assign = false;
            // }
            DB::beginTransaction();
            $bag->update();
            DB::commit();
            return responseMsg(true,"Bag Update","");
        }catch(MyException $e){
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            return responseMsg(false,"Server Error",$e->getMessage());
        }
    }

    public function deleteBag(Request $request){
        try{
            $rules=[
                "id"=>"required|exists:".$this->_M_BagPacking->getTable().",id"
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $bag = $this->_M_BagPacking->find($request->id);
            $order = $bag->getOrderDtl();
            $bagStatus = flipConstants(collect(Config::get("customConfig.bagStatus")));
            if($bag->packing_status==$bagStatus["dispatched"]){
                throw new MyException("Bag is Dispatch");
            }
            if($order && $order->is_wip_disbursed){
                throw new MyException("can't Delete the bag because order is verified");
            }
            $message = "Bag Deleted";
            if($bag->packing_status==$bagStatus["in godown"]){
                $bag->packing_status = $bagStatus["in factory"];
                $message.=" From Godown";
            }else{
                $bag->lock_status = true;
                $message.=" From Stock";
            }
            DB::beginTransaction();
            $bag->update();
            DB::commit();
            return responseMsg(true,$message,"");
        }catch(MyException $e){
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            return responseMsg(false,"Server Error","");
        }
    }

    public function generateChalan(Request $request){
        try{            
            // return $pdf->output(); 
            $bags = $this->_M_BagPacking->whereIn("id",collect($request->bag)->pluck("id"))->get();
            $bags->map(function($val){
                $order = $val->getOrderDtl();
                $val->units = $order->units;
                $val->bag_type = $order->getBagType()->bag_type;
                $val->bag_color = collect(json_decode($order->bag_color,true))->implode(","); 
                $val->bag_size = (float)$order->bag_w." x ".(float)$order->bag_l.($order->bag_g ?(" x ".(float)$order->bag_g) :"");                
                return $val;
            });
            $parentTable=[];
            $unitGroup = $bags->groupBy(["units"]);
            foreach($unitGroup as $unit=>$bb){
                $bagGroup = $bb->groupBy(["bag_type","bag_color","bag_size"]);
                $table=[];
                $table["grand_total"]=[
                    "total"=>$bb->count(),
                    "total_unit"=>$unit =="Kg" ? collect($bb)->sum("packing_weight") : collect($bb)->sum("packing_bag_pieces"),
                ];
                foreach($bagGroup as $bagType=>$colorSize){
                    foreach($colorSize as $color=>$size){
                        foreach($size as $key=>$val){
                            $table["row"][]=[
                                "bag_type"=>$bagType,
                                "color"=>$color,
                                "size"=>$key,
                                "bags"=>$val->toArray(),
                                "count"=>collect($val)->count(),
                                "total_unit"=>$unit =="Kg" ? collect($val)->sum("packing_weight") : collect($val)->sum("packing_bag_pieces"),
                            ];
                        }
                    }
                }
                $parentTable[$unit]=$table;
            }
            // dd($parentTable);

            $firstBag = $bags->first();
            $order = $firstBag->getOrderDtl();
            $client = $order->client_detail_id==1 && $request->bookingForClientId ? $this->_M_ClientDetails->find($request->bookingForClientId): $order->getClient();
            $rateType = $order->getRateType();
            if($request->rateTypeId){
                $rateType= $this->_M_RateType->find($request->rateTypeId);
            }
            $auto = $this->_M_Auto->find($request->autoId);
            $transposer = $this->_M_Transporter->find($request->transporterId);
            $fyear=getFY();
            list($fromDate,$uptoDate) = explode("-",$fyear);
            $fromDate=$fromDate."-04-01"; 
            $uptoDate=$uptoDate."-03-31"; 
            $transPortStatus = (Config::get("customConfig.transportType.".$request->transPortType));         
            $count = $this->getChalaneSequence($transPortStatus);
            $chalanNo="OO"."-";
            if($transPortStatus==4){
                $chalanNo="FC"."-";
                if(in_array($bags->first()->packing_status,[2,5])){
                    $chalanNo="GC"."-";
                }
            }
            elseif($transPortStatus==1){
                $chalanNo="GF"."-";
            }
            elseif($transPortStatus==3){
                $chalanNo="FG"."-";
                if(in_array($bags->first()->packing_status,[2,5])){
                    $chalanNo="GG"."-";
                }
            }
            $key=$chalanNo;
            $chalanNo .=substr($rateType ? Str::upper($rateType->rate_type) :"O",0,1)."-";
            // $chalanNo .=str_pad((string)$count,4,"0",STR_PAD_LEFT); 
            $chalanNo .=$request->chalanNo; 
            
            if($transPortStatus==3){
                $godownDtl = Config::get("customConfig.godownDtl");
                foreach($godownDtl as $key=>$val){
                    $client->$key=$val;
                }
            }
            if($client){
                $client->address = $request->address??$client->address;
            }
            $data["unique_id"]=getFY()."-".$key.$count;
            $data["table"]=$parentTable;
            $data["chalan_date"]=$request->dispatchedDate??Carbon::now()->format("d-m-Y");
            $data["transposer"]=$transposer;
            $data["bus_no"]=$request->busNo;
            $data["is_local"]=$request->isLocalTransport;
            $data["auto"]=$auto;
            $data["chalan_no"] = $chalanNo;
            $data["client"] = $client;
            $pdf = Pdf::loadView('pdf.template', $data);
            $pdfContent = $pdf->output();
            $data["pdf_base64"]= base64_encode($pdfContent);
            $newRequest = new Request(
                [
                    "unique_id"=> $data["unique_id"],
                    "chalan_date"=> Carbon::parse($data["chalan_date"])->format("Y-m-d"),
                    "chalan_no"=>$chalanNo,
                    "chalan_json"=>$data,
                    "user_id"=>Auth()->user()->id,
                ]                
            );
            $this->_M_ChalanDtl->store($newRequest);
            return responseMsgs(true,"Chalane Genrated",$data);
        }catch(MyException $e){
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            dd($e->getMessage(),$e->getLine());
            return responseMsg(false,"Server Error","");
        }
    }

    public function viewChalan($unique_id){
        try{
            $chalan = $this->_M_ChalanDtl->where("unique_id",$unique_id)->first();
            $data = json_decode($chalan->chalan_json,true);
            if(!$data["pdf_base64"]){
                $pdf = Pdf::loadView('pdf.template', $data);
                $pdfContent = $pdf->output();
                $data["pdf_base64"]= base64_encode($pdfContent);
            }
            return responseMsg(true,"Chalan Preview",$data);
        }catch(MyException $e){
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            return responseMsg(false,"Server Error!!","");
        }
    }

    public function bagGodown($godownTypeId,Request $request){
        $user = Auth()->user();
        $user_type = $user->user_type;
        if($request->ajax()){
            $data = $this->_M_BagPacking->select("order_punch_details.*","bag_packings.*",
                        "bag_type_masters.bag_type","client_detail_masters.client_name"
                    )
                    ->join("order_punch_details","order_punch_details.id","bag_packings.order_id")
                    ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
                    ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")                    
                    ->where("bag_packings.lock_status",false)
                    ->orderBy("bag_packings.order_id","DESC")
                    ->orderBy("bag_packings.id","DESC");
            if($godownTypeId==1){
                $data->where("bag_packings.packing_status",2);
            }elseif($godownTypeId==2){
                $data->where("bag_packings.packing_status",5);
            }
            $data=$data->get();
            $request->merge(["cli"=>true]);
            $verificationPending = $this->reivingGodown($godownTypeId,$request);
            $summary=[
                "totalWeight"=>roundFigure($data->sum("packing_weight")),
                "intTransPort"=>$verificationPending->sum("total_unverified_bag"),
            ];
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('packing_date', function ($val) { 
                    return $val->packing_date ? Carbon::parse($val->packing_date)->format("d-m-Y") : "";
                })
                ->addColumn('bag_color', function ($val) { 
                    return collect(json_decode($val->bag_color,true))->implode(",") ;
                })
                ->addColumn('bag_size', function ($val) { 
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn("reiving_date",function($val){
                    return $val->godown_reiving_date ? Carbon::parse($val->godown_reiving_date)->format("d-m-Y"):"";
                })
                ->addColumn('bag_gsm', function ($val) { 
                    return collect(json_decode($val->bag_gsm,true))->implode(",");
                })
                ->addColumn('action', function ($val) {                    
                    $button="";
                    if((!$val->is_wip_disbursed) && (!$val->is_delivered) ){
                        $button.='<button class="btn btn-sm btn-primary" onClick="editBag('.$val->id.')" >E</button>';
                        $button.='<button class="btn btn-sm btn-danger" onClick="showConfirmDialog('."'Are you sure you want to Delete this item?', function() { deleteBag($val->id); })".'" >D</button>';
                    } 
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->with($summary)
                ->make(true);
            return $list;

        }
        $data["godownTypeId"] = $godownTypeId;
        $data["autoList"] =$this->_M_Auto->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["rateType"] = $this->_M_RateType->all();
        $data["clientList"] = $this->_M_ClientDetails->getClientListOrm()->where("id","<>",1)->orderBy("client_name","ASC")->get();
        return view("Packing/godown",$data);
    }

    public function reivingGodown($godownTypeId,Request $request){
        if($request->ajax()){
            $data = $this->_M_PackTransport->select("bag_packing_transports.id","bag_packing_transports.bill_no","bag_packing_transports.invoice_no","bag_packing_transports.transport_date",
                        "auto_details.auto_name",
                        DB::raw(
                            "
                            COUNT(bag_packing_transport_details.bag_packing_id) AS total_bag,
                            COUNT( CASE WHEN bag_packing_transport_details.is_delivered = FALSE THEN bag_packing_transport_details.bag_packing_id END ) AS total_unverified_bag
                            "
                        )
                    )
                    ->join("bag_packing_transport_details","bag_packing_transport_details.pack_transport_id","bag_packing_transports.id")
                    ->leftJoin("auto_details","auto_details.id","bag_packing_transports.auto_id")
                    ->where("bag_packing_transports.transport_status",3)
                    ->where("bag_packing_transport_details.lock_status",false)
                    ->where("bag_packing_transports.is_fully_reviewed",false)
                    ->where("bag_packing_transports.lock_status",false)
                    ->groupBy("bag_packing_transports.id","bag_packing_transports.bill_no","bag_packing_transports.invoice_no","bag_packing_transports.transport_date","auto_details.auto_name");
            if($godownTypeId==1){
                $data->where(function($query)use($godownTypeId){
                    $query->where("bag_packing_transports.godown_type_id",$godownTypeId)
                    ->orWhereNull("bag_packing_transports.godown_type_id");
                });
            }else{                
                $data->where("bag_packing_transports.godown_type_id",$godownTypeId);
            }
            if($request->cli){
                return $data->get();
            }
                
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('transport_date', function ($val) { 
                    return $val->transport_date ? Carbon::parse($val->transport_date)->format("d-m-Y") : "";
                })
                ->addColumn('action', function ($val) {                 
                    return '<button class="btn btn-sm btn-info" onClick="openReceivingModel('.$val->id.')" >Verify</button>';
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;
        }
        $data["godownTypeId"] = $godownTypeId;
        return view("Packing/reivingGodown",$data);
    }

    public function reivingTransport(Request $request){
        try{
            $client = $this->_M_ClientDetails->all();
            $bagType = $this->_M_BagType->all();
            $data = $this->_M_TransportDetail->select("bag_packings.*","order_punch_details.*","bag_packing_transport_details.*")
                    ->join("bag_packings","bag_packings.id","bag_packing_transport_details.bag_packing_id")
                    ->join("order_punch_details","order_punch_details.id","bag_packings.order_id")
                    ->where("bag_packing_transport_details.pack_transport_id",$request->id)
                    ->where("bag_packing_transport_details.lock_status",false)
                    ->where("bag_packing_transport_details.is_delivered",false)
                    ->get()
                    ->map(function($val)use($client,$bagType){
                        $val->client_name = $client->firstWhere("id",$val->client_detail_id)->client_name??"";
                        $val->bag_type = $bagType->firstWhere("id",$val->bag_type_id)->bag_type??"";
                        $val->bag_size = (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"");
                        $val->bag_color = collect(json_decode($val->bag_color,true))->implode(",") ;
                        return $val;
                    });
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function addInGodown(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                "id"=>"required|exists:".$this->_M_TransportDetail->getTable().",id,is_delivered,false",
                "godownTypeId"=>"required|int|in:1,2"
            ]);
            if($validate->fails()){
                return validationError($validate);
            }
            $status = Config::get("customConfig.godownBagStatus.".$request->godownTypeId);
            $currentDate = Carbon::now()->format("Y-m-d");
            $user = Auth()->user();

            $transportDtl = $this->_M_TransportDetail->find($request->id);
            $bagPackage = $this->_M_BagPacking->find($transportDtl->bag_packing_id);
            $transport = $this->_M_PackTransport->find($transportDtl->pack_transport_id);

            $transportDtl->is_delivered = true;
            $transportDtl->reiving_user_id = $user->id;
            $transportDtl->reiving_date = $currentDate;

            $bagPackage->packing_status = $status;
            $bagPackage->godown_reiving_date = Carbon::now();
            
            DB::beginTransaction();
            $transportDtl->update();
            $bagPackage->update();
            $test = $this->_M_TransportDetail->where("is_delivered",false)->where("pack_transport_id",$transportDtl->pack_transport_id)->count("id");
            if($test==0){
                $transport->is_fully_reviewed = true;
                $transport->reiving_date = $currentDate;
                $transport->reiving_user_id = $user->id;
            }
            $transport->update();
            DB::commit();
            return responseMsgs(true,"Bag Verify","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function bagTransport(Request $request){
        $flag = $request->flag;
        if($request->ajax()){
            // return $this->bagGodown($request);
        }
        $data["autoList"] =$this->_M_Auto->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["flag"] = $flag;
        return view("Packing/transport",$data);
    }

    public function bagStockToGodown(Request $request){
        $flag = $request->flag;
        if($request->ajax()){
            return $this->bagStock($request);
        }
        $data["autoList"] =$this->_M_Auto->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->where("lock_status",false)->orderBy("id","ASC")->get();
        $data["flag"] = $flag;
        return view("Packing/transportStoke",$data);
    }

    public function addBagInTransport(Request $request){        
        return view("Packing/inTransport");
    }

    public function searchPackingForTransport(Request $request){
        try{
            $packingStatus = [];
            if($request->status=="For Godown"){
                $packingStatus = [1];
            }
            if($request->status=="For Delivery"){
                $packingStatus = [1,2];
            }
            DB::enableQueryLog();
            $data = $this->_M_BagPacking
                    ->select("order_punch_details.*","bag_packings.*",
                        "bag_type_masters.bag_type","client_detail_masters.client_name"
                    )
                    ->join("order_punch_details","order_punch_details.id","bag_packings.order_id")
                    ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
                    ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")
                    ->where("bag_packings.packing_no",$request->packingNo)
                    ->whereIn("bag_packings.packing_status",$packingStatus)
                    ->whereIn("bag_packings.packing_status",[1,2])
                    ->where("bag_packings.lock_status",false)
                    ->first();
            // dd(DB::getQueryLog());
            if($data){
                $data->printing_color = collect(json_decode($data->printing_color,true))->implode(",");
            }
            return responseMsgs(true,"Data Fetch",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function transportAdd(Request $request){
        try{
            $user = Auth()->user();
            $rules = [
                "transPortType" => "required|in:For Godown,For Delivery,For Factory", // Fixed 'id' to 'in' for a set of allowed values
                "dispatchedDate" => "required|date", // Ensures dispatchedDate is a valid date
                "invoiceNo" => "required", // Invoice number is mandatory
                "godownTypeId" => "required_if:transPortType,For Godown", 
                "bag" => "required|array", // Packing must be a non-empty array
                "bag.*.id" => [
                        "required",
                        Rule::exists($this->_M_BagPacking->getTable(), "id")
                            ->whereIn("packing_status", [1, 2, 5]),
                    ], // Ensures the packing IDs exist with specific statuses
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $transportStatus = Config::get("customConfig.transportType.".$request->transPortType);
            $firstBag= $this->_M_BagPacking->find(collect($request->bag)->first()["id"]);
            $request->merge(["userId"=>$user->id,"transportStatus"=>$transportStatus,"transport_init_status"=>$firstBag->packing_status]);
            DB::beginTransaction();
            $tranId = $this->_M_PackTransport->store($request);
            $orderId=collect();
            foreach($request->bag as $val){
                $packing = $this->_M_BagPacking->find($val["id"]);
                $orderId->push($packing->order_id);
                $newRequest = new Request($val);
                $newRequest->merge([
                    "packTransportId"=>$tranId,
                    "bagPackingId"=>$packing->id,
                ]);
                $this->_M_TransportDetail->store($newRequest);
                $packing->packing_status = $transportStatus ;
                $packing->update();
            }
            if(Config::get("customConfig.transportType.For Delivery")==$transportStatus){
                $orderId= $orderId->unique();
                foreach($orderId as $val){
                    $order = $this->_M_OrderPunchDetail->find($val);
                    $totalDelivered = $this->_M_BagPacking
                                    ->where("order_id",$val)
                                    ->where("packing_status",Config::get("customConfig.transportType.For Delivery"))
                                    ->get();
                    $totalUnit = $totalDelivered->sum("packing_weight");
                    if($order->units!="Kg"){
                        $totalUnit = $totalDelivered->sum("packing_bag_pieces");
                    }                    
                    $bookedRoll = $order->getRollDetail()->get();
                    $totalGarbage = 0;
                    $garbagePossibleBagPiece =0;
                    $rollWeight = 0;
                    $bag = $order->getBagType();
                    $bagPiecesFormula = $bag->roll_find;
                    foreach($bookedRoll as $roll){
                        $acceptedGarbage = $roll->getAcceptedGarbage()->sum("total_qtr");
                        $notAcceptedGarbage = $roll->getNotAcceptedGarbage()->sum("total_qtr");
                        $totalGarbage +=($acceptedGarbage+$notAcceptedGarbage);
                        
                        $newPiecesRequest = new Request($roll->toArray());
                        $newPiecesRequest->merge([
                            "formula"=>$bagPiecesFormula,
                            "bookingBagUnits"=>"Pieces",
                            "length" => $roll->length,
                            "netWeight" => $roll->net_weight,
                            "size" => $roll->size,
                            "gsm" => $roll->gsm,
                            "bagL"=> $order->bag_l,
                            "bagW"=> $order->bag_w,
                            "bagG"=> $order->bag_g,
                        ]);
                        $result = $this->calculatePossibleProduction($newPiecesRequest);
                        $garbagePec = (($acceptedGarbage+$notAcceptedGarbage)/$roll->net_weight);
                        
                        if($garbagePec){
                            $garbagePossibleBagPiece += $result["result"] * $garbagePec;
                        }

                    }
                    if(($order->total_units - $order->disbursed_units)<=round($totalUnit + ($order->units=="Kg" ? $totalGarbage : $garbagePossibleBagPiece))){
                        $order->is_delivered = true;
                        $order->delivery_date = Carbon::now()->format("Y-m-d");
                    }
                    if($order->client_detail_id==1 && $request->bookingForClientId){
                        $order->client_detail_id = $request->bookingForClientId;
                    }
                    if($request->rateTypeId){
                        $order->rate_type_id=$request->rateTypeId;
                    }
                    $order->update();
                }
            }  
            DB::commit();
            return responseMsgs(true,"Dag is Dispatched","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function transportRegister_old(Request $request){
        
        if($request->ajax())
        {
            $data = $this->_M_PackTransport->select("bag_packing_transports.*","auto_details.auto_name",
                        "transporter_details.transporter_name"
                    )
                    ->leftJoin("auto_details","auto_details.id","bag_packing_transports.auto_id")
                    ->leftJoin("transporter_details","transporter_details.id","bag_packing_transports.transporter_id")
                    ->where("bag_packing_transports.lock_status",false)
                    ->orderBy("bag_packing_transports.id","DESC");

            if($request->fromDate && $request->uptoDate){
                $data->WhereBetween("bag_packing_transports.transport_date",[$request->fromDate,$request->uptoDate]);
            }elseif($request->fromDate){
                $data->Where("bag_packing_transports.transport_date",$request->fromDate);
            }elseif($request->uptoDate){
                $data->Where("bag_packing_transports.transport_date",$request->uptoDate);
            }

            if($request->autoId){
                $data->where("bag_packing_transports.auto_id",$request->autoId);
            }

            if($request->transporterId){
                $data->where("bag_packing_transports.transporter_id",$request->transporterId);
            }
            if($request->billNo){
                $data->where("bag_packing_transports.bill_no",$request->billNo);
            }
            if($request->invoiceNo){
                $data->where("bag_packing_transports.invoice_no",$request->invoiceNo);
            }
            if($request->transportTypeId){
                $data->where(function($query) use($request){
                    foreach($request->transportTypeId as $index=> $val){
                        $statusType=Config::get("customConfig.transportationDropDownType.".$val);
                        if ($statusType) { // Ensure config exists to prevent errors
                            $query->orWhere(function ($q) use ($statusType) {
                                $q->where("bag_packing_transports.transport_status", $statusType["transport_status"])
                                  ->where("bag_packing_transports.transport_init_status", $statusType["transport_init_status"]);
                            });
                        }
            
                    }
                });
            }
            
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("transition_type",function($val){
                    $transition_type = "";
                    if($val->transport_status==3){
                        $transition_type="factory to godown";
                    }
                    if($val->transport_status==4){
                        $transition_type="dispatched for delivery";
                    }
                    return $transition_type;
                })
                ->addColumn('transport_date', function ($val) { 
                    return $val->transport_date ? Carbon::parse($val->transport_date)->format("d-m-Y") : "";
                })
                ->addColumn('bag_no', function ($val) { 
                    return collect($val->getBag()->get())->pluck("packing_no")->implode(" , ") ;
                })
                ->addColumn('client_name', function ($val) { 
                    $bag = $val->getBag()->get();
                    $orderId =$bag->unique("order_id")->pluck("order_id");
                    $order = $this->_M_OrderPunchDetail->whereIn("id",$orderId)->get();
                    $clineId = $order->unique("client_detail_id")->pluck("client_detail_id");
                    $client = $this->_M_ClientDetails->whereIn("id",$clineId)->get();
                    return collect($client)->pluck("client_name")->implode(" , ") ;
                })
                ->addColumn('action', function ($val) {                   
                    $button='<button class="btn btn-sm btn-info" onClick="openPreviewChalanModel('."'".$val->chalan_unique_id."'".')" >Chalan</button>';
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        $data["autoList"] = $this->_M_Auto->getAutoListOrm()->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->getAutoListOrm()->orderBy("id","ASC")->get();
        $data["transportType"] = collect(Config::get("customConfig.transportationDropDownType"))->map(function($val,$index){
            return json_decode(json_encode(["id"=>$index,"type"=>$index]));
        });
        return view("Packing/bag_transport",$data);
    }

    public function transportRegister(Request $request){
        
        if($request->ajax())
        {
            $data = $this->_M_TransportDetail->select("bag_packing_transport_details.*","bag_packing_transports.transport_date","bag_packing_transports.invoice_no",
                        "bag_packing_transports.chalan_unique_id",
                        "bag_packing_transports.transport_status","bag_packing_transports.transport_init_status",
                        "bag_packings.packing_no","bag_packings.packing_weight","bag_packings.packing_bag_pieces",
                        "auto_details.auto_name","client_detail_masters.client_name",
                        "order_punch_details.order_no","order_punch_details.order_date","order_punch_details.bag_gsm","order_punch_details.bag_printing_color",
                        "order_punch_details.bag_w","order_punch_details.bag_l","order_punch_details.bag_g","order_punch_details.bag_color",
                        "bag_type_masters.bag_type",
                        "transporter_details.transporter_name"
                    )
                    ->join("bag_packing_transports","bag_packing_transports.id","bag_packing_transport_details.pack_transport_id")
                    ->join("bag_packings","bag_packings.id","bag_packing_transport_details.bag_packing_id")
                    ->join("order_punch_details","order_punch_details.id","bag_packings.order_id")
                    ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")
                    ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
                    ->leftJoin("auto_details","auto_details.id","bag_packing_transports.auto_id")
                    ->leftJoin("transporter_details","transporter_details.id","bag_packing_transports.transporter_id")
                    ->where("bag_packing_transports.lock_status",false)
                    ->where("bag_packing_transport_details.lock_status",false)
                    ->orderBy("bag_packing_transports.transport_date","DESC");

            if($request->fromDate && $request->uptoDate){
                $data->WhereBetween("bag_packing_transports.transport_date",[$request->fromDate,$request->uptoDate]);
            }elseif($request->fromDate){
                $data->Where("bag_packing_transports.transport_date",$request->fromDate);
            }elseif($request->uptoDate){
                $data->Where("bag_packing_transports.transport_date",$request->uptoDate);
            }

            if($request->autoId){
                $data->where("bag_packing_transports.auto_id",$request->autoId);
            }

            if($request->transporterId){
                $data->where("bag_packing_transports.transporter_id",$request->transporterId);
            }
            if($request->billNo){
                $data->where("bag_packing_transports.bill_no",$request->billNo);
            }
            if($request->invoiceNo){
                $data->where("bag_packing_transports.invoice_no",$request->invoiceNo);
            }
            if($request->transportTypeId){
                $data->where(function($query) use($request){
                    foreach($request->transportTypeId as $index=> $val){
                        $statusType=Config::get("customConfig.transportationDropDownType.".$val);
                        if ($statusType) { // Ensure config exists to prevent errors
                            $query->orWhere(function ($q) use ($statusType) {
                                $q->where("bag_packing_transports.transport_status", $statusType["transport_status"])
                                  ->whereIn("bag_packing_transports.transport_init_status", $statusType["transport_init_status"]);
                            });
                        }
            
                    }
                });
            }
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("transition_type",function($val){
                    return $transition_type = collect(Config::get("customConfig.transportationDropDownType"))
                                                ->filter(function ($item) use ($val) {
                                                    return $item["transport_status"] == $val->transport_status &&
                                                        in_array($val->transport_init_status, $item["transport_init_status"]);
                                                })
                                                ->first()["type"] ?? "";
                    
                })
                ->addColumn('transport_date', function ($val) { 
                    return $val->transport_date ? Carbon::parse($val->transport_date)->format("d-m-Y") : "";
                })
                ->addColumn("bag_printing_color",function($val){
                    return collect(json_decode($val->bag_printing_color,true))->implode(",") ;
                })
                ->addColumn('bag_color', function ($val) { 
                    return collect(json_decode($val->bag_color,true))->implode(",") ;
                })
                ->addColumn('bag_size', function ($val) { 
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn('bag_gsm', function ($val) { 
                    return collect(json_decode($val->bag_gsm,true))->implode(",");
                })
                ->addColumn('action', function ($val) {                   
                    $button='<button class="btn btn-sm btn-info" onClick="openPreviewChalanModel('."'".$val->chalan_unique_id."'".')" >View Chalan</button>';
                    if(in_array(Auth()->user()->user_type_id,[1,2])){
                        // $button.='<button class="btn btn-sm btn-danger" onclick="showConfirmDialog('."'Are you sure you want to deactivate this item?', function() { deleteTransPortDtl('$val->id'); })".'" >Delete</button>';
                    }
                    $deliveryStatus = Config::get("customConfig.transportType.For Delivery");
                    if(in_array(Auth()->user()->user_type_id,[1,2]) && $val->transport_status==$deliveryStatus){
                        if(!$val->is_bag_return){
                            $button.='<button class="btn btn-sm btn-danger" onclick="showConfirmDialog('."'Are you sure you want to Sell Return this item?', function() { sellRollBak('$val->id'); })".'" >Sell Return</button>';
                        }else{
                            $button.='<span class="btn btn-sm btn-warning">Bag Is Return in '.($val->transport_init_status==5?"Godown 2":($val->transport_init_status==2?"Godown 1":"Factory")).'</span>';
                        }
                    }
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        $data["autoList"] = $this->_M_Auto->getAutoListOrm()->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->getAutoListOrm()->orderBy("id","ASC")->get();
        $data["transportType"] = collect(Config::get("customConfig.transportationDropDownType"))->map(function($val,$index){
            return json_decode(json_encode(["id"=>$index,"type"=>$index]));
        });
        return view("Packing/bag_transport",$data);
    }

    public function deleteTransPortDtl($id,Request $request){
        try{
            
            $tranportDtl = $this->_M_TransportDetail->find($id);
            $tranportDtl->lock_status=true;
            $testOther = $this->_M_TransportDetail->where("pack_transport_id",$tranportDtl->pack_transport_id)->where("lock_status",false)->where("id","<>",$tranportDtl->id)->count();
            $transport = $this->_M_PackTransport->find($tranportDtl->pack_transport_id);
            DB::beginTransaction();
            if($testOther==0){
                $transport->lock_status = true;
                $transport->update();
            }
            $tranportDtl->update();
            DB::commit();
            return responseMsg(true,"Bag Delete From Transport","");
        }catch(MyException $e){
            DB::rollBack();
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsg(false,"Server error!!!","");
        }
    }

    public function returnSell($id,Request $request){
        try{
            
            $tranportDtl = $this->_M_TransportDetail->find($id);
            $transport = $this->_M_PackTransport->find($tranportDtl->pack_transport_id);
            $bag = $this->_M_BagPacking->find($tranportDtl->bag_packing_id);
            $tranportDtl->is_bag_return=true;
            $bag->packing_status = $transport->transport_init_status;
            DB::beginTransaction();  
            $bag->update();          
            $tranportDtl->update();
            DB::commit();
            return responseMsg(true,"Bag Return","");
        }catch(MyException $e){
            DB::rollBack();
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsg(false,"Server error!!!","");
        }
    }

    public function bagHistory(Request $request){
        if($request->ajax())
        {
            $data = $this->_M_BagPacking
                    ->select("bag_packings.*","order_punch_details.estimate_delivery_date","order_punch_details.order_date","bag_w","bag_l","bag_g","client_detail_masters.client_name")
                    ->join("order_punch_details","order_punch_details.id","bag_packings.order_id")
                    ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
                    ->where("bag_packings.lock_status",false)
                    ->orderBy("bag_packings.id","DESC");

            if($request->fromDate && $request->uptoDate){
                $data->WhereBetween("bag_packings.packing_date",[$request->fromDate,$request->uptoDate]);
            }elseif($request->fromDate){
                $data->Where("bag_packings.packing_date",$request->fromDate);
            }elseif($request->uptoDate){
                $data->Where("bag_packings.packing_date",$request->uptoDate);
            }

            if($request->bagStatusId){
                $data->where("bag_packings.packing_status",$request->bagStatusId);
            }
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("packing_date",function($val){
                    return $val->packing_date ? Carbon::parse($val->packing_date)->format("d-m-Y"): "" ;
                })
                ->addColumn("estimate_delivery_date",function($val){
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y"): "" ;
                })
                ->addColumn("order_date",function($val){
                    return $val->order_date ? Carbon::parse($val->order_date)->format("d-m-Y"): "" ;
                })
                ->addColumn('bag_size', function ($val) { 
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn('bag_status', function ($val) { 
                    return Config::get("customConfig.bagStatus.".$val->packing_status);
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }

        $data["bagStatus"]= collect(Config::get("customConfig.bagStatus"))->map(function($val,$index){
            return json_decode(json_encode(["id"=>$index,"type"=>$val]));
        });
        return view("Packing/bag_history",$data);
    }

    public function chalanRegister(Request $request){
        if($request->ajax()){
            $data = $this->_M_PackTransport->select("bag_packing_transports.*",
                        "bag_packing_transport_details.total_bags","bag_packing_transport_details.total_return_bags",
                        "auto_details.auto_name", "transporter_details.transporter_name"
                    )
                    ->join(DB::raw("(
                            select pack_transport_id, count(bag_packing_id) AS total_bags, 
                                count(case when is_bag_return=true then bag_packing_id else null end) AS total_return_bags
                            from bag_packing_transport_details
                            where bag_packing_transport_details.lock_status = false
                            group by pack_transport_id
                            ) AS bag_packing_transport_details"),"bag_packing_transport_details.pack_transport_id","bag_packing_transports.id")
                    ->leftJoin("auto_details","auto_details.id","bag_packing_transports.auto_id")
                    ->leftJoin("transporter_details","transporter_details.id","bag_packing_transports.transporter_id")
                    ->where("bag_packing_transports.lock_status",false)
                    ->orderBy("bag_packing_transports.transport_date","DESC");

            if($request->fromDate && $request->uptoDate){
                $data->WhereBetween("bag_packing_transports.transport_date",[$request->fromDate,$request->uptoDate]);
            }elseif($request->fromDate){
                $data->Where("bag_packing_transports.transport_date",$request->fromDate);
            }elseif($request->uptoDate){
                $data->Where("bag_packing_transports.transport_date",$request->uptoDate);
            }

            if($request->autoId){
                $data->where("bag_packing_transports.auto_id",$request->autoId);
            }

            if($request->transporterId){
                $data->where("bag_packing_transports.transporter_id",$request->transporterId);
            }
            if($request->billNo){
                $data->where("bag_packing_transports.bill_no",$request->billNo);
            }
            if($request->invoiceNo){
                $data->where("bag_packing_transports.invoice_no",$request->invoiceNo);
            }
            if($request->transportTypeId){
                $data->where(function($query) use($request){
                    foreach($request->transportTypeId as $index=> $val){
                        $statusType=Config::get("customConfig.transportationDropDownType.".$val);
                        if ($statusType) {
                            // Ensure config exists to prevent errors
                            $query->orWhere(function ($q) use ($statusType) {
                                $q->where("bag_packing_transports.transport_status", $statusType["transport_status"])
                                  ->whereIn("bag_packing_transports.transport_init_status", $statusType["transport_init_status"]);
                            });
                        }
            
                    }
                });
            }

            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("transition_type",function($val){
                    return $transition_type = collect(Config::get("customConfig.transportationDropDownType"))
                        ->filter(function ($item) use ($val) {
                            return $item["transport_status"] == $val->transport_status &&
                                in_array($val->transport_init_status, $item["transport_init_status"]);
                        })
                        ->first()["type"] ?? "";
                    
                })
                ->addColumn('transport_date', function ($val) { 
                    return $val->transport_date ? Carbon::parse($val->transport_date)->format("d-m-Y") : "";
                })
                ->make(true);
            return $list;
        }
        $data["autoList"] = $this->_M_Auto->getAutoListOrm()->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->getAutoListOrm()->orderBy("id","ASC")->get();
        $data["transportType"] = collect(Config::get("customConfig.transportationDropDownType"))->map(function($val,$index){
            return json_decode(json_encode(["id"=>$index,"type"=>$index]));
        });
        return view("Packing/chalanRegister",$data);
    }

    public function transPortDtlHtml($id,Request $request){
        try{
            $transport = $this->_M_PackTransport->find($id);
            $transportDtl = $this->_M_TransportDetail->where("pack_transport_id",$id)->where("lock_status",false)->get();
            $transport->total_bag = $transportDtl->count();
            $transport->total_return_bag = $transportDtl->where("is_bag_return",true)->count();
            $transport->transition_type = collect(Config::get("customConfig.transportationDropDownType"))
                                            ->filter(function ($item) use ($transport) {
                                                return $item["transport_status"] == $transport->transport_status &&
                                                    in_array($transport->transport_init_status, $item["transport_init_status"]);
                                            })
                                            ->first()["type"] ?? "";
            $auto = $this->_M_Auto->find($transport->auto_id);
            $transporter = $this->_M_Transporter->find($transport->transporter_id);
            $transport->auto_name = $auto->auto_name??"";
            $transport->transporterLabel = $transporter ? ($transporter->is_bus ? "Bus Name : " : "Transporter Name : ") : "";
            $transport->transporter_name = $transporter->transporter_name??"";
            $transport->gstLabel = $transporter ? ($transporter->is_bus ? "Bus No : " : "GST No : ") : "";
            $transport->gst_no = $transporter->gst_no??"";


            $bags = $this->_M_BagPacking->whereIn("id",$transportDtl->pluck("bag_packing_id"))->get();
            $order = $this->_M_OrderPunchDetail->whereIn("id",$bags->pluck("order_id"))->get()->map(function($val)use($bags,$transportDtl){
                $val->clientDtl = $this->_M_ClientDetails->find($val->client_detail_id); 
                if($val->clientDtl){
                    $val->clientDtl->sector = $this->_M_Sector->find($val->clientDtl->sector_id)->sector??"";
                    $val->clientDtl->state = $val->clientDtl->getState()->state??"";
                    $val->clientDtl->city = $val->clientDtl->getCity()->city??"";
                }
                $val->bags = $bags->where("order_id",$val->id)->map(function($val)use($transportDtl){
                    $returnBag = $transportDtl->where("is_bag_return",true)->where("bag_packing_id",$val->id)->first();
                    $val->is_bag_return = $returnBag ? true:false;
                    return $val;
                });
                return $val;
            });
            $data["transport"]=$transport;
            $data["transportDtl"]=$transportDtl;
            $data["auto"]=$auto;
            $data["transporter"]=$transporter;
            $data["order"]=$order;
            $html = view('Packing.Parts.transportDtl', $data)->render();
            return responseMsg(true,"html",$html);

        }catch(MyException $e){
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            dd($e);
            return responseMsg(false,"Server Error","");
        }
    }
}
