<?php

namespace App\Http\Controllers;

use App\Imports\ImportDispatchHistory;
use App\Imports\OrderImport;
use App\Imports\OrderRollMapImport;
use App\Models\BagPacking;
use App\Models\CityStateMap;
use App\Models\ClientDetailMaster;
use App\Models\FareDetail;
use App\Models\GradeMaster;
use App\Models\OrderBroker;
use App\Models\OrderPunchDetail;
use App\Models\RateTypeMaster;
use App\Models\RollDetail;
use App\Models\RollTransit;
use App\Models\Sector;
use App\Models\StateMaster;
use App\Models\StereoDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Str;

class ImportOldRecords extends Controller
{
    //
    protected $_M_OrderPunchDetail;
    protected $_M_ClientDetail;
    protected $_M_GradeMaster;
    protected $_M_FareDetail;
    protected $_M_StereoDetail;
    protected $_M_RateTypeMaster;
    protected $_M_RollDetail;
    protected $_M_RollTransit;
    protected $_M_Broker;
    protected $_M_StateMaster;
    protected $_M_CityStateMap;
    protected $_M_Sector;
    protected $_M_BagPacking;
    function __construct()
    {
        $this->_M_OrderPunchDetail = new OrderPunchDetail();
        $this->_M_ClientDetail = new ClientDetailMaster();
        $this->_M_GradeMaster = new GradeMaster();
        $this->_M_FareDetail = new FareDetail();
        $this->_M_StereoDetail = new StereoDetail();
        $this->_M_RateTypeMaster = new RateTypeMaster();
        $this->_M_RollDetail = new RollDetail();
        $this->_M_RollTransit = new RollTransit();
        $this->_M_Broker = new OrderBroker();
        $this->_M_StateMaster = new StateMaster();
        $this->_M_CityStateMap = new CityStateMap();
        $this->_M_Sector = new Sector();
        $this->_M_BagPacking = new BagPacking();
    }

    public function importOrders(Request $request){
        if($request->post()){
            ini_set('max_execution_time', 600);
            $validate = Validator::make($request->all(),["csvFile"=>"required|mimes:csv,xlsx"]);
            if($validate->fails()){
                return validationError($validate);
            }
            $file = $request->file('csvFile');
            $headings = (new HeadingRowImport())->toArray($file)[0][0];
            $expectedHeadings = Config::get("customConfig.orderImportCsvHeader");
            if (array_diff($expectedHeadings, $headings)) {
                return responseMsgs(false,"data in invalid Formate","");;
            }
            $rows = Excel::toArray([], $file);

            // Validate rows
            $validationErrors = [];
            foreach ($rows[0] as $index => $row) {
                // Skip the header row
                if ($index == 0) continue;
                // Validate each row
                $rowData = array_combine($headings, $row);
                if(strtolower($file->getClientOriginalExtension())=="xlsx")
                {
                    $rowData["order_date"] = is_int($rowData["order_date"])? getDateColumnAttribute($rowData['order_date']) : $rowData['order_date'];
                    $rowData["estimate_delivery_date"] = is_int($rowData["estimate_delivery_date"])? getDateColumnAttribute($rowData['estimate_delivery_date']) : $rowData['estimate_delivery_date'];
                }
                $validator = Validator::make($rowData, [
                    "order_no"=>"required",
                    'client_name' => "required",                   
                    'order_date' => 'required|date',
                    'estimate_delivery_date' => 'nullable|date',
                    'bag_type' => 'required|in:D,U,L,B,LBB',
                    'bag_quality' => 'nullable|in:NW,BOPP,LAM',
                    "bag_gsm"=>[
                        "required",
                        function($attribute, $value, $fail) use ($rowData) {
                            if (isset($rowData['bag_quality']) && $rowData['bag_quality'] === 'BOPP') {
                                if (!preg_match('/^\d+\+\d+\+\d+$/', $value)) {
                                    $fail("The $attribute format must be '35+13+12' (three numbers separated by '+').");
                                }
                            }
                            if (isset($rowData['bag_quality']) && $rowData['bag_quality'] === 'LAM') {
                                if (!preg_match('/^\d+\+\d+$/', $value)) {
                                    $fail("The $attribute format must be '35+13' (three numbers separated by '+').");
                                }
                            }
                            if ($rowData['bag_quality'] === 'NW') {
                                if (!(is_numeric($value) && floor($value) == $value)) {
                                    $fail("The $attribute must be an integer.");
                                }
                            }
            
                        }
                    ],
                    "units"=>"required|in:Kg,Piece",
                    "total_units"=>"nullable|numeric",
                    "booked_units"=>"nullable|numeric",
                    "rate_per_unit"=>"required|numeric",
                    "bag_w"=>"required|numeric",
                    "bag_l"=>"required|numeric",
                    "bag_g"=>"required_if:bag_type,B|numeric",
                    "rate_type" => "nullable|exists:".$this->_M_RateTypeMaster->getTable().",rate_type",
                    'fare_type' => "nullable|exists:".$this->_M_FareDetail->getTable().",fare_type",
                    'stereo_type' => "nullable|exists:".$this->_M_StereoDetail->getTable().",stereo_type",                    
                    "bag_printing_color"=>"nullable",
                    "agent_name"=>"nullable|exists:".$this->_M_Broker->getTable().",broker_name",
                    "is_delivered"=>"nullable|bool",
                ]);

                if ($validator->fails()) {
                    $validationErrors[$index] = $validator->errors()->all();
                }
                $dataWithHeadings[] = $rowData; 
            }

            $group = collect($dataWithHeadings)->groupBy("order_no")->filter(function($val){
                return $val->count()>1;
            });
            
            if($group->count()>0){
                foreach($group as $index=>$val){
                    $validationErrors[] = ["Order no $index is repeated ".sizeof($val)." time"];
                }
            }
            if (!empty($validationErrors)) {
                return responseMsgs(false,"Validation Error",$validationErrors);
            }   
            $clineName = collect($dataWithHeadings)->pluck("client_name")->unique();     
            
            // Import the CSV file using the RollDetailsImport class
            DB::beginTransaction();
            foreach($clineName as $val){
                if(!$this->_M_ClientDetail->where("client_name",$val)->first()){
                    $clientRequest = new Request(["client_name"=>$val]);
                    $this->_M_ClientDetail->store($clientRequest);
                }
            }            
            Excel::import(new OrderImport, $file);
            DB::commit();
            return responseMsgs(true,"data import","");
        }
        return view("import/order");
    }

    public function orderRollMap(Request $request){
        if($request->post()){
            $validate = Validator::make($request->all(),["csvFile"=>"required|mimes:csv,xlsx"]);
            if($validate->fails()){
                return validationError($validate);
            }
            $file = $request->file('csvFile');
            $headings = (new HeadingRowImport())->toArray($file)[0][0];
            $expectedHeadings = Config::get("customConfig.orderRollMapCsvHeader");
            if (array_diff($expectedHeadings, $headings)) {
                return responseMsgs(false,"data in invalid Formate","");;
            }
            $rows = Excel::toArray([], $file);

            // Validate rows
            $validationErrors = [];
            foreach ($rows[0] as $index => $row) {
                // Skip the header row
                if ($index == 0) continue;
                // Validate each row
                $rowData = array_combine($headings, $row);                
                $validator = Validator::make($rowData, [
                    "order_no"=>"required|exists:".$this->_M_OrderPunchDetail->getTable().",order_no,is_delivered,false",
                    'roll_no' => [
                        "required",
                        function($attribute, $value, $fail)use ($rowData,$index ){
                            $rollExists = $this->_M_RollDetail
                                ->where("roll_no", $value)
                                ->whereNull("client_detail_id")
                                ->exists();

                            $transitExists = $this->_M_RollTransit
                                ->where("roll_no", $value)
                                ->whereNull("client_detail_id")
                                ->exists();

                            if (!$rollExists && !$transitExists) {
                                $fail("The $attribute ($value) is invalid.");
                            }
                        },
                    ],
                ]);
                if ($validator->fails()) {           
                    $validationErrors[$index] = $validator->errors()->all();
                }
                $dataWithHeadings[] = $rowData; 
            }

            $group = collect($dataWithHeadings)->groupBy("roll_no")->filter(function($val){
                return $val->count()>1;
            });
            
            if($group->count()>0){
                foreach($group as $index=>$val){
                    $validationErrors[] = ["Roll No $index is repeated ".sizeof($val)." time"];
                }
            }
            if (!empty($validationErrors)) {
                return responseMsgs(false,"Validation Error",$validationErrors);
            }  
            
            // Import the CSV file using the RollDetailsImport class
            DB::beginTransaction();            
            Excel::import(new OrderRollMapImport, $file);
            DB::commit();
            return responseMsgs(true,"data import","");
        }
        return view("import/orderRollMap");
    }

    public function importDespatchHistory(Request $request){
        if($request->post()){
            ini_set('max_execution_time', 600);
            $validate = Validator::make($request->all(),["csvFile"=>"required|mimes:csv,xlsx"]);
            if($validate->fails()){
                return validationError($validate);
            }
            $file = $request->file('csvFile');
            $headings = (new HeadingRowImport())->toArray($file)[0][0];
            Excel::import(new ImportDispatchHistory,$file);
        }
        return view("import/importExcelToDb");
    }

    public function importClient(Request $request){
        if($request->post()){
            ini_set('max_execution_time', 600);
            $validate = Validator::make($request->all(),["csvFile"=>"required|mimes:csv,xlsx"]);
            if($validate->fails()){
                return validationError($validate);
            }
            $file = $request->file('csvFile');
            $headings = (new HeadingRowImport())->toArray($file)[0][0];
            $expectedHeadings = Config::get("customConfig.clientCsvHeader");
            if (array_diff($expectedHeadings, $headings)) {
                return responseMsgs(false,"data in invalid Formate","");;
            }
            $rows = Excel::toArray([], $file);

            // Validate rows
            $validationErrors = [];
            $dataWithHeadings = [];
            foreach ($rows[0] as $index => $row) {
                // Skip the header row
                if ($index == 0) continue;
                // Validate each row
                $rowData = array_combine($headings, $row);               
                $validator = Validator::make($rowData, [
                    "client_name"=>"required|unique:".$this->_M_ClientDetail->getTable().",client_name",
                    'mobile_no' => "required",                   
                    'sector' => 'nullable',
                    'secondary_mobile_no' => 'nullable',
                    'temporary_mobile_no' => 'nullable',
                    'email' => 'nullable|email',
                    "state"=>"nullable",
                    "city"=>"nullable",
                    "address"=>"nullable",
                    "trade_name"=>"nullable",
                ]);

                if ($validator->fails()) {
                    $validationErrors[$index] = $validator->errors()->all();
                }
                $rowData["client_name"] = Str::title($rowData["client_name"]);
                $rowData["state"] = Str::title(trim($rowData["state"]));
                $rowData["city"] = Str::title(trim($rowData["city"]));
                $rowData["sector"] = Str::title(trim($rowData["sector"]));
                $dataWithHeadings[] = $rowData; 
            }

            $group = collect($dataWithHeadings)->groupBy("client_name")->filter(function($val){
                return $val->count()>1;
            });
            
            if($group->count()>0){
                foreach($group as $index=>$val){
                    $validationErrors[] = ["Client Name $index is repeated ".sizeof($val)." time"];
                }
            }
            if (!empty($validationErrors)) {
                return responseMsgs(false,"Validation Error",$validationErrors);
            } 
            
            // Import the CSV file using the RollDetailsImport class
            $state = $this->_M_StateMaster->all();
            $city = $this->_M_CityStateMap->all();
            $sector = $this->_M_Sector->all();
            DB::beginTransaction();
            foreach($dataWithHeadings as $val){
                $stateId = $state->where("state_name",$val["state"])->first()->id??null;
                $cityId = $city->where("state_id",$stateId)->where("city_name",$val["city"])->first()->id??null;
                $sectorId = $sector->where("sector",$val["sector"])->first()->id??null;
                if(!$stateId && $val["state"]){
                    $newStateRequest = new Request(["state_name"=>$val["state"]]);
                    $stateId = $this->_M_StateMaster->store($newStateRequest);
                    $state = $this->_M_StateMaster->all();
                }
                if(!$cityId && $val["city"]){
                    $newCityRequest = new Request(["state_id"=>$stateId,"city_name"=>$val["city"]]);
                    $cityId = $this->_M_CityStateMap->store($newCityRequest);
                    $city = $this->_M_CityStateMap->all();
                }
                if(!$sectorId && $val["sector"]){
                    $newSectorRequest = new Request(["sector"=>$val["sector"]]);
                    $sectorId = $this->_M_Sector->store($newSectorRequest);
                    $sector = $this->_M_Sector->all();
                }
                $clientRequest = new Request($val);
                $clientRequest->merge(["state_id"=>$stateId,"city_id"=>$cityId,"sector_id"=>$sectorId]);
                $this->_M_ClientDetail->store($clientRequest);
            }   
            DB::commit();
        }
        return view("import/importExcelToDb");
    }

    public function importBag(Request $request){
        if($request->post()){
            ini_set('max_execution_time', 600);
            $validate = Validator::make($request->all(),["csvFile"=>"required|mimes:csv,xlsx"]);
            if($validate->fails()){
                return validationError($validate);
            }
            $file = $request->file('csvFile');
            $headings = (new HeadingRowImport())->toArray($file)[0][0];
            $expectedHeadings = Config::get("customConfig.BagCsvHeader");
            if (array_diff($expectedHeadings, $headings)) {
                return responseMsgs(false,"data in invalid format","");;
            }
            $rows = Excel::toArray([], $file);

            // Validate rows
            $validationErrors = [];
            $dataWithHeadings = [];
            $orderBroker = OrderBroker::all();
            $rateType = RateTypeMaster::all();
            foreach ($rows[0] as $index => $row) {
                // Skip the header row
                if ($index == 0) continue;
                // Validate each row
                $rowData = array_combine($headings, $row);    
                if(strtolower($file->getClientOriginalExtension())=="xlsx")
                {
                    $rowData["packing_date"] = is_int($rowData["packing_date"])? getDateColumnAttribute($rowData['packing_date']) : $rowData['packing_date'];
                }           
                $validator = Validator::make($rowData, [
                    // "packing_no"=>"required|unique:".$this->_M_BagPacking->getTable().",packing_no",
                    "bora_number"=>"nullable|unique:".$this->_M_BagPacking->getTable().",packing_no",
                    "packing_date"=>"required|date",
                    'client_name' => "required",                   
                    'bag_configuration' => 'required|in:NW,BOPP,LAM',
                    'bag_type' => 'required|in:B,D,L,U,LBB,NO-D',
                    'gsm' => 'nullable|numeric',
                    'w' => 'required|numeric',
                    "l"=>"required|numeric",
                    "g"=>"nullable|numeric",
                    "bag_color"=>"nullable",
                    "printing_color"=>"nullable",
                    "bag_weight"=>"required|numeric",
                    "bag_in_pieces"=>"nullable|int",
                    "bag_status"=>"required|in:Godown1,Godown2,Factory"
                ]);

                if ($validator->fails()) {
                    $validationErrors[$index] = $validator->errors()->all();
                }
                $brokerId = $orderBroker->firstWhere(fn($b) => strtoupper($b->broker_name) === strtoupper($rowData["agent"]))->id ?? null;

                if (!$brokerId && $rowData["agent"]) {
                    $brokerId = (new OrderBroker())->store(["broker_name" => $rowData["agent"]]);
                    // If needed again, you may refresh the broker list or just use the created ID
                    $orderBroker = OrderBroker::all();
                }

                $rateTypeId = $rateType->firstWhere(fn($r) => strtoupper($r->rate_type) === strtoupper($rowData["rate_type"]))->id ?? null;;
                $rowData["client_name"] = Str::title($rowData["client_name"]);
                $rowData["packing_weight"]=$rowData["bag_weight"];  
                $rowData["broker_id"]=$brokerId; 
                $rowData["rate_type_id"]=$rateTypeId;                 
                $rowData["packing_no"]=trim($rowData["bora_number"]) ? Carbon::parse($rowData["packing_date"])->format("d/m/y")."-00".trim($rowData["bora_number"]):null;
                $dataWithHeadings[] = $rowData; 
            }

            $group = collect($dataWithHeadings)->whereNotNull("packing_no")->groupBy("packing_no")->filter(function($val){
                return $val->count()>1;
            });
            
            if($group->count()>0){
                foreach($group as $index=>$val){
                    $validationErrors[] = ["packing no $index is repeated ".sizeof($val)." time"];
                }
            }

            if (!empty($validationErrors)) {
                return responseMsgs(false,"Validation Error",$validationErrors);
            } 
            
            // Import the CSV file using the RollDetailsImport class
            $client = $this->_M_ClientDetail->all();
            $bagType = Config::get("customConfig.bagTypeIdByShortName");
            $data = collect($dataWithHeadings)->groupBy(["client_name","bag_configuration","bag_type","gsm","bag_color","printing_color"]);
            DB::beginTransaction();
            foreach($data as $clintName=>$bagConfigVal){ 
                foreach($bagConfigVal as $config =>$bagVal){
                    foreach($bagVal as $bag=>$gsmVal){
                        foreach($gsmVal as $gsm=>$colorVal){
                            foreach($colorVal as $color=>$printingVal){
                                foreach($printingVal as $print=>$item){
                                    $item = $item->groupBy(["w","l","g"]);
                                    foreach($item as $w=>$litem){
                                        foreach($litem as $l=>$gitem){
                                            foreach($gitem as $g=>$val){                                                
                                                $inKgItem     = $val->whereNull("bag_in_pieces");
                                                $inPiecesItem = $val->whereNotNull("bag_in_pieces");
                                                $client_detail_id = $client->where("client_name",$clintName)->first()->id??null;
                                                if(!$client_detail_id){
                                                    $clientRequest = new Request(["client_name"=>$clintName]);
                                                    $client_detail_id = $this->_M_ClientDetail->store($clientRequest);
                                                    $client = $this->_M_ClientDetail->all();
                                                }
                                                $orderDate = $val->min("packing_date");
                                                $bagTypeId = $bagType[$bag]??null;
                                                $orderData=[
                                                    "client_detail_id"=>$client_detail_id,
                                                    "estimate_delivery_date"=>Carbon::now()->format("Y-m-d"),
                                                    "order_date"=>$orderDate,
                                                    "bag_type_id"=>$bagTypeId,
                                                    "bag_quality"=>$config,
                                                    "bag_gsm"=>$gsm ? explode(",",$gsm):null,
                                                    "bag_w"=>$w,
                                                    "bag_l"=>$l,
                                                    "bag_g"=>$g?$g:null,
                                                    "bag_color"=>$color?explode(",",$color):null,
                                                    "bag_printing_color"=>$print?explode(",",$print):null,
                                                    "disbursed_units"=>0,
                                                    "wip_disbursed_units"=>0,
                                                    "is_wip_disbursed"=>true,
                                                ];
                                                $newOrderRequest = new Request($orderData);
                                                if($inKgItem){
                                                    $newOrderRequest->merge([
                                                        "booked_units"=>$inKgItem->sum("bag_weight"),
                                                        "units"=>"Kg",
                                                        "total_units"=>$inKgItem->sum("bag_weight"),
                                                    ]);
                                                    $orderId = $this->_M_OrderPunchDetail->store($newOrderRequest);
                                                    foreach($inKgItem as $kgs){
                                                        $newBagRequest = new Request($kgs);
                                                        $newBagRequest->merge(["order_id"=>$orderId]);
                                                        if($kgs["bag_status"]=="Godown1" || $kgs["bag_status"]=="Godown2"){
                                                            $newBagRequest->merge(["packing_status"=>2,"godown_reiving_date"=>$kgs["packing_date"]]);
                                                            if($kgs["bag_status"]=="Godown2"){
                                                                $newBagRequest->merge(["packing_status"=>5]);
                                                            }
                                                        }
                                                        $this->_M_BagPacking->store($newBagRequest);
                                                    }
                                                }
                                                if($inPiecesItem){
                                                    $newOrderRequest->merge([
                                                        "booked_units"=>$inPiecesItem->sum("bag_weight"),
                                                        "units"=>"Kg",
                                                        "total_units"=>$inPiecesItem->sum("bag_weight"),
                                                    ]);
                                                    $orderId = $this->_M_OrderPunchDetail->store($newOrderRequest);
                                                    foreach($inPiecesItem as $kgs){
                                                        $newBagRequest = new Request($kgs);
                                                        $newBagRequest->merge(["order_id"=>$orderId]);
                                                        if($kgs["bag_status"]=="Godown1" || $kgs["bag_status"]=="Godown2"){
                                                            $newBagRequest->merge(["packing_status"=>2,"godown_reiving_date"=>$kgs["packing_date"]]);
                                                            if($kgs["bag_status"]=="Godown2"){
                                                                $newBagRequest->merge(["packing_status"=>5]);
                                                            }
                                                        }
                                                        $this->_M_BagPacking->store($newBagRequest);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }    
                            }
                        }
                    }
                }
            }  
            DB::commit();
            flashToast("message","Bag Import Successfully");
            return redirect()->back();
        }
        return view("import/importExcelToDb");
    }
}
