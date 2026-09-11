<?php

namespace App\Http\Controllers;

use App\Exceptions\MyException;
use App\Exports\ExportRoll;
use App\Imports\RollDetailsImport;
use App\Models\AutoDetail;
use App\Models\BagType;
use App\Models\BagTypeMaster;
use App\Models\ChalanDtl;
use App\Models\ClientDetail;
use App\Models\ClientDetailMaster;
use App\Models\ColorMaster;
use App\Models\CuttingScheduleDetail;
use App\Models\FareDetail;
use App\Models\GarbageAcceptRegister;
use App\Models\GarbageEntry;
use App\Models\GarbageNotAcceptRegister;
use App\Models\GradeMaster;
use App\Models\LoopDetail;
use App\Models\LoopStock;
use App\Models\LoopUsageAccount;
use App\Models\MachineMater;
use App\Models\OrderBroker;
use App\Models\OrderPunchDetail;
use App\Models\OrderRollBagType;
use App\Models\PendingOrderBagType;
use App\Models\PrintingEntry;
use App\Models\PrintingMachine;
use App\Models\PrintingRegister;
use App\Models\PrintingScheduleDetail;
use App\Models\RateTypeMaster;
use App\Models\RollColorMaster;
use App\Models\RollDetail;
use App\Models\RollPrintColor;
use App\Models\RollQualityGradeMap;
use App\Models\RollQualityMaster;
use App\Models\RollTransit;
use App\Models\RollTransport;
use App\Models\RollTransportDetail;
use App\Models\StereoDetail;
use App\Models\TransporterDetail;
use App\Models\User;
use App\Models\VendorDetail;
use App\Models\VendorDetailMaster;
use App\Traits\Formula;
use App\Traits\Rolls;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Yajra\DataTables\Facades\DataTables;

class RollController extends Controller
{
    use Formula;
    use Rolls;

    private $_M_RollDetail;
    private $_M_VendorDetail;
    private $_M_PrintingScheduleDetail;
    private $_M_ClientDetails;
    private $_M_BagType;
    private $_M_Machine;

    private $_M_RollTransit;
    private $_M_RollColor;
    private $_M_CuttingScheduleDetail;
    private $_M_Color;
    private $_M_OrderPunches;
    private $_M_OrderRollBagType;
    private $_M_PendingOrderBagType;
    protected $_M_User;
    protected $_M_GarbageAcceptRegister;
    protected $_M_GarbageNotAcceptRegister;

    protected $_M_GradeMaster;
    protected $_M_FareDetail;
    protected $_M_StereoDetail;
    protected $_M_RateTypeMaster;
    protected $_M_RollQualityMaster;
    protected $_M_RollQualityGradeMap;
    protected $_M_LoopStock;
    protected $_M_LoopDetail;
    protected $_M_LoopAccount;
    protected $_M_OrderBroker;
    protected $_M_GarbageEntry;
    protected $_M_PrintingEntry ;
    protected $_M_PrintingRegister;
    protected $_M_Auto;
    protected $_M_Transporter;
    protected $_M_ChalanDtl;
    protected $_M_RollTransport;
    protected $_M_RollTransportDetail;

    function __construct()
    {
        $this->_M_User = new User();
        $this->_M_RollTransit = new RollTransit();
        $this->_M_RollColor = new RollColorMaster();
        $this->_M_VendorDetail= new VendorDetailMaster();
        $this->_M_ClientDetails = new ClientDetailMaster();
        $this->_M_BagType = new BagTypeMaster();
        $this->_M_RollDetail = new RollDetail();
        $this->_M_PrintingScheduleDetail = new PrintingScheduleDetail();
        $this->_M_Machine = new MachineMater();
        $this->_M_CuttingScheduleDetail = new CuttingScheduleDetail();
        $this->_M_Color = new ColorMaster();
        $this->_M_OrderPunches = new OrderPunchDetail();
        $this->_M_OrderRollBagType = new OrderRollBagType();
        $this->_M_PendingOrderBagType = new PendingOrderBagType();
        $this->_M_GarbageAcceptRegister = new GarbageAcceptRegister();
        $this->_M_GarbageNotAcceptRegister = new GarbageNotAcceptRegister();
        $this->_M_GradeMaster = new GradeMaster();
        $this->_M_FareDetail = new FareDetail();
        $this->_M_StereoDetail = new StereoDetail();
        $this->_M_RateTypeMaster = new RateTypeMaster();
        $this->_M_RollQualityMaster = new RollQualityMaster();
        $this->_M_RollQualityGradeMap = new RollQualityGradeMap();
        $this->_M_LoopDetail = new LoopDetail();
        $this->_M_LoopStock = new LoopStock();
        $this->_M_LoopAccount = new LoopUsageAccount();
        $this->_M_OrderBroker = new OrderBroker();
        $this->_M_GarbageEntry = new GarbageEntry();
        $this->_M_PrintingEntry = new PrintingEntry();
        $this->_M_PrintingRegister = new PrintingRegister();
        $this->_M_Auto  =  new AutoDetail();
        $this->_M_Transporter = new TransporterDetail();
        $this->_M_ChalanDtl = new ChalanDtl();
        $this->_M_RollTransport = new RollTransport();
        $this->_M_RollTransportDetail = new RollTransportDetail();
        
    }

    #================ Roll Transit =====================

    public function addTransitRoll(Request $request){
        try{
            
            if($request->getMethod()=="POST"){ 
                $rule = [
                    "purchaseDate"=>"nullable|",
                    "venderId"=>"required|exists:".$this->_M_VendorDetail->getTable().",id,lock_status,false",
                    "size"=>"required|numeric|min:0.1",
                    "gsm"=>"required|numeric|min:0.01",
                    "rollColor"=>"required|exists:".$this->_M_RollColor->getTable().",color,lock_status,false",
                    "length"=>"required|numeric|min:0.1",
                    "netWeight"=>"required|numeric|min:0.1",
                    "grossWeight"=>"required|numeric|min:0.1",
                    "forClientId"=>"nullable".($request->forClientId?"|exists:".$this->_M_ClientDetails->getTable().",id":""),
                    "estimatedDespatchDate"=>"required_with:forClientId",
                ];
                $validate = Validator::make($request->all(),$rule);
                if($validate->fails()){
                    return validationError($validate);
                }    
                $request->merge([
                    "rollType"=>"NW",
                    "estimateDeliveryDate"=>$request->estimatedDespatchDate,
                    "bagTypeId"=>$request->bookingBagTypeId,
                    "bagUnit"=>$request->bookingBagUnits,
                    "printingColor"=>$request->bookingPrintingColor,
                ]);  
                DB::beginTransaction();   
                $id = $this->_M_RollTransit->store($request);                
                $roll = $this->_M_RollTransit->find($id);
                if($roll->client_detail_id){
                    $newRequest = new Request($roll->toArray());
                    $orderId = $this->_M_OrderPunches->store($request);
                    $newRequest->merge(["order_id"=>$orderId,"roll_id"=>$roll->id]);
                    $this->_M_OrderRollBagType->store($newRequest);
                }
                DB::commit();
                flashToast("message","New Roll Add");
                return responseMsgs(true,"New Roll Added",["rollDtl"=>$roll]);
            }
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function transitList(Request $request){        
        $flag= $request->flag;
        $user_type = Auth()->user()->user_type_id??"";
        $data["user_type"] = $user_type;
        $data["flag"]=$flag;
        $data["items"] = $this->_M_RollTransit
                        ->select("roll_transits.vender_id","roll_transits.purchase_date","roll_transits.vehicle_no",
                            "vendor_detail_masters.vendor_name",
                            DB::raw("count(roll_transits.id) as total_count,
                                    sum(roll_transits.net_weight) as total_net_weight,
                                    count(CASE WHEN roll_transits.size <=2 then roll_transits.id END) as total_loop,
                                    COUNT(CASE WHEN roll_transits.gsm_variation <= (-8/100.0) OR roll_transits.gsm_variation >= (8/100.0)  THEN roll_transits.id END) AS total_deviation,
                                    count( CASE WHEN roll_transits.client_detail_id IS NOT NULL THEN roll_transits.id END ) as total_book 
                            ")
                        )
                        ->join("vendor_detail_masters","vendor_detail_masters.id","roll_transits.vender_id")
                        ->where("roll_transits.lock_status",false)
                        ->groupBy("roll_transits.vender_id","roll_transits.purchase_date","vendor_detail_masters.vendor_name","roll_transits.vehicle_no")
                        ->orderBy("roll_transits.purchase_date")
                        ->get()
                        ->map(function($val){
                            $val->purchase_date = Carbon::parse($val->purchase_date)->format("d-m-Y");
                            $val->deletesAction = "
                                showConfirmDialog('Are You Sure Want To Delete', function(){ deleteTransit('".$val->vender_id."','".$val->purchase_date."','".$val->vehicle_no."');});
                            ";
                            return $val;
                        }); 
        return view("Roll/transit",$data);
    }

    public function transitDtl($vendor_id,Request $request){
        $user_type = Auth()->user()->user_type_id;        
        $data =[];
        $data["user_type"] = $user_type;
        $data["addToRollInStock"] = false;
        if(in_array($user_type,[1,2])){
            $data["addToRollInStock"] = true;
        }
        if($request->ajax()){                            
                $data = $this->_M_RollTransit->select("roll_transits.*","vendor_detail_masters.vendor_name",
                            "client_detail_masters.client_name","bag_type_masters.bag_type",
                            DB::raw("roll_transits.gsm_variation * 100 as gsm_variation")
                        )                            
                        ->join("vendor_detail_masters","vendor_detail_masters.id","roll_transits.vender_id")
                        ->leftJoin("client_detail_masters","client_detail_masters.id","roll_transits.client_detail_id")
                        ->leftJoin("bag_type_masters","bag_type_masters.id","roll_transits.bag_type_id")
                        ->where("roll_transits.lock_status",false)
                        ->orderBy("roll_transits.id","DESC");
                if($request->purchase_date){
                    $request->merge(["purchase_date"=>Carbon::parse($request->purchase_date)->format("Y-m-d")]);
                    $data->where("roll_transits.purchase_date",$request->purchase_date);
                }
                if($request->vehicle_no){
                    $data->where("roll_transits.vehicle_no",$request->vehicle_no);
                }
                if($vendor_id){
                    $data->where("roll_transits.vender_id",$vendor_id);
                } 
                               
                if ($request->has('export')) {
                    // Skip pagination when exporting
                    $data = $data->get();
                    return Excel::download(new ExportRoll($data), 'roll.xlsx');
                }

                // Handling search
                if ($request->has('search')) {
                    $search = $request->search['value'];  // search term from DataTables
                    
                    $data = $data->where(function ($query) use ($search) {

                        $query->where("roll_transits.roll_no","LIKE", "%$search%")
                            ->orWhere("roll_transits.purchase_date","LIKE", "%$search%")
                            ->orWhere("roll_transits.size","LIKE", "%$search%")
                            ->orWhere("roll_transits.gsm","LIKE", "%$search%")
                            ->orWhere("roll_transits.roll_color","LIKE", "%$search%")
                            ->orWhere("roll_transits.length","LIKE", "%$search%")
                            ->orWhere("roll_transits.net_weight","LIKE", "%$search%")
                            ->orWhere("roll_transits.gross_weight","LIKE", "%$search%")
                            ->orWhere('vendor_detail_masters.vendor_name', 'LIKE', "%$search%")
                            ->orWhere('client_detail_masters.client_name', 'LIKE', "%$search%")
                            ->orWhere('bag_type_masters.bag_type', 'LIKE', "%$search%");  // Assuming ststop is a field to search
                    });
                }
                $data = $data->get();
                $summary=[
                    "totalWeight"=> roundFigure($data->sum("net_weight")),
                ];
                $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn("check",function ($val) {
                        return "<input type='checkbox' name='transitId[]' value='" . $val->id . "' /> " ;
                    })
                    ->addColumn('row_color', function ($val) {
                        $color = "";
                        $gsmVariationPer = $val->gsm_variation;
                        if(!is_between($gsmVariationPer,-8,8)){
                            $color="tr-gsm_variation_danger";
                        }
                        elseif(!is_between($gsmVariationPer,-4,4)){
                            $color="tr-gsm_variation";
                        }
                        
                        return $color;
                    })
                    ->addColumn("bag_size",function ($val) {
                        return $val->bag_type_id ? ((float)$val->w." x ".(float)$val->l.($val->g?(" x ".(float)$val->g):"")):null;
                    })
                    ->addColumn("size",function ($val) {
                        return $val->size>2 ? $val->size:"Loop";
                    })
                    ->addColumn("grade",function($val){
                        $quality = RollQualityMaster::find($val->quality_id);
                        $grade = $quality ? $quality->getGrade()->first()->grade??"":"";
                        return $grade ;                        
                    })
                    ->addColumn("quality",function($val){
                        $quality = RollQualityMaster::find($val->quality_id);
                        return $quality->quality??"" ;                        
                    })
                    ->addColumn('gsm_variation', function ($val) {                        
                        return roundFigure($val->gsm_variation)."%";
                    })
                    ->addColumn('color', function ($val) {
                        return collect(json_decode($val->printing_color,true))->implode(",");
                    })                    
                    ->addColumn("purchase_date",function($val){
                        return $val->purchase_date ? Carbon::parse($val->purchase_date)->format("d-m-Y") : "";                        
                    })
                    ->addColumn("estimate_delivery_date",function($val){
                        return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                        
                    })
                    ->addColumn("delivery_date",function($val){
                        return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                        
                    })
                    ->addColumn("printing_date",function($val){
                        return $val->printing_date ? Carbon::parse($val->printing_date)->format("d-m-Y") : "";                        
                    })
                    ->addColumn("cutting_date",function($val){
                        return $val->cutting_date ? Carbon::parse($val->cutting_date)->format("d-m-Y") : "";                        
                    })
                    ->addColumn("gsm_json",function($val){
                        return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                    })
                    ->addColumn('action', function ($val) {                    
                        $button = "";
                        if($val->is_roll_cut){
                            return $button;
                        }
                        if($val->size<=2){
                            return $button;
                        }
                        if(!$val->client_detail_id){
                            $button .= '<button class="btn btn-sm btn-warning" onClick="openModelBookingModel('.$val->id.')" >Book</button>';
                        }if($val->client_detail_id && !$val->is_printed){
                            $button .= '<button class="btn btn-sm btn-danger" onClick="openModelBookingModel('.$val->id.')" >Alter Booking</button>';
                            $button .= '<button class="btn btn-sm btn-warning" onClick="removeBooking('.$val->id.')" >Remove</button>';
                        }
                        return $button;
                    })
                    ->rawColumns(['action','color',"check"])
                    ->with($summary)
                    ->make(true);
                return $list;

        }
        $data["purchase_date"] = $request->purchase_date;
        $data["vender"]        = $this->_M_VendorDetail->find($vendor_id);
        return view("Roll/transitDtl",$data);
    }

    public function deleteTransit(Request $request){
        try{
            DB::beginTransaction();
            $this->_M_RollTransit
                ->where("vender_id",$request->vendorId)
                ->where("purchase_date",Carbon::parse($request->purchaseDate)->format("Y-m-d"))
                ->where("vehicle_no",$request->vehicleNo)
                ->update(["lock_status"=>true]);
            DB::commit();
            return responseMsgs(true,"Roll Delete","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }


    
    function rollTransitBook(Request $request){
        try{
            $rule=[
                "rollId"=>"required|exists:".$this->_M_RollTransit->getTable().",id,lock_status,false",
                "bookingForClientId"=>"required|exists:".$this->_M_ClientDetails->getTable().",id,lock_status,false",
                "bookingEstimatedDespatchDate"=>"required|date",
                "bookingBagUnits"=>"required|in:Kg,Piece",
                "bookingBagTypeId"=>"required|exists:".$this->_M_BagType->getTable().",id",
                "bookingPrintingColor"=>"required|array",
                "bookingPrintingColor.*"=>"required",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }

            $request->merge([
                "clientDetailId"=>$request->bookingForClientId,
                "estimateDeliveryDate"=>$request->bookingEstimatedDespatchDate,
            ]);

            $roll = $this->_M_RollTransit->find($request->rollId);
            $roll->client_detail_id = $request->bookingForClientId;
            $roll->estimate_delivery_date = $request->bookingEstimatedDespatchDate;
            $roll->bag_type_id = $request->bookingBagTypeId;
            $roll->bag_unit = $request->bookingBagUnits;
            $roll->w = $request->w;
            $roll->l = $request->l;
            $roll->g = $request->g;
            $roll->printing_color = $request->bookingPrintingColor; 
            $roll->loop_color = $request->looColor;
                      
            $newRequest = new Request($roll->toArray());
            
            DB::beginTransaction();
            $roll->update();
            $orderId = $this->_M_OrderPunches->store($request);
            $newRequest->merge(["order_id"=>$orderId,"roll_id"=>$roll->id]);
            $this->_M_OrderRollBagType->store($newRequest);
            DB::commit();
            return responseMsgs(true,"Roll No. ".$roll->roll_no." is Booked","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    function rollTransitRollStock(Request $request){
        try{

            foreach($request->items as $val){
                $rollTransit = $this->_M_RollTransit->find($val);
    
                if($rollTransit){
                    DB::beginTransaction();
                    if($rollTransit->size>2){
                        $rollDtl =$rollTransit->replicate();
                        $rollDtl->setTable($this->_M_RollDetail->getTable());
                        $rollDtl->id =  $rollTransit->id;
                        $rollDtl->created_at =  $rollTransit->created_at;
                        $rollDtl->updated_at =  $rollTransit->updated_at;
                        $rollDtl->deleted_at =  $rollTransit->deleted_at;
                        $rollDtl->roll_receiving_at =  Carbon::now();
                        $rollDtl->save();
                    }else{
                        $rollTransit->loop_color = $rollTransit->roll_color;
                        $loop = array_filter($rollTransit->getAttributes(), function ($value, $key) {
                            
                            return in_array($key,$this->_M_LoopDetail->getFillable()); // Example: Filter out null values
                        },ARRAY_FILTER_USE_BOTH);               
                        $rollDtl =$this->_M_LoopDetail->newInstance($loop);
                        $rollDtl->setTable($this->_M_LoopDetail->getTable());
                        $rollDtl->id =  $rollTransit->id;
                        $rollDtl->created_at =  $rollTransit->created_at;
                        $rollDtl->updated_at =  $rollTransit->updated_at;
                        $rollDtl->deleted_at =  $rollTransit->deleted_at;
                        $rollDtl->roll_receiving_at =  Carbon::now();
                        $rollDtl->save(); 
                        $loopStock = $this->_M_LoopStock->where("loop_color",$rollDtl->loop_color)->first();
                        if(!$loopStock){
                            $newRequest = new Request(
                                [
                                    "loop_color"=>$rollDtl->loop_color,
                                ]
                            );
                            $loopStockId = $this->_M_LoopStock->store($newRequest);
                            $loopStock = $this->_M_LoopStock->find($loopStockId);
                        }
                        $newLoopAccRequest = new Request(
                            [
                                "loop_stock_id"=>$loopStock->id,
                                "loop_id"=>$rollDtl->id,
                                "description"=>"new roll add",
                                "opening_balance"=>$loopStock->balance,
                                "credit"=>0,
                                "debit"=>$rollDtl->net_weight,
                                "balance"=>$loopStock->balance +  $rollDtl->net_weight,
                                "user_id"=>Auth()->user()->id
                            ]
                        );
                        $this->_M_LoopAccount->store($newLoopAccRequest);
                        $loopStock->balance =  $loopStock->balance +  $rollDtl->net_weight;   
                        $loopStock->update();                  
                    }

                    $rollTransit->delete();
                    DB::commit();
                }
            }
            return responseMsgs(true,"Rolls Added On Stock","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }

    }

    #=========== end Roll Transit ======================

    public function downloadCsvTemplate()
    {
        
        // Define the CSV headers (columns)
        $headers = Config::get("customConfig.rollImportCsvHeader");

        // Sample data for the CSV
        $data=[];
        foreach($headers as $val){
            $data[0][]="";
            $data[1][]="";
        }

        // Open a temporary file in memory to write the CSV content
        $file = fopen('php://temp', 'w');

        // Write the header row to the CSV file
        fputcsv($file, $headers);

        // Write the data rows to the CSV file
        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        // Move the pointer back to the start of the file
        rewind($file);

        // Get the content of the CSV file
        $csvContent = stream_get_contents($file);

        // Close the file
        fclose($file);

        // Return the CSV content with the correct MIME type and headers
        return response($csvContent)
            ->header('Content-Type', 'text/csv')  // Ensure correct MIME type for CSV
            ->header('Content-Disposition', 'attachment; filename="roll_template.csv"'); 
    }

    public function importRoll(Request $request){
        try{
            $dataWithHeadings=[];
            ini_set('max_execution_time', 600);
            $validate = Validator::make($request->all(),["csvFile"=>"required|mimes:csv,xlsx"]);
            if($validate->fails()){
                return validationError($validate);
            }
            $file = $request->file('csvFile');
            $headings = (new HeadingRowImport())->toArray($file)[0][0];
            $expectedHeadings = Config::get("customConfig.rollImportCsvHeader");
            // $expectedHeadings = ['vendor_name','vehicle_no', 'purchase_date',"quality", 'roll_size',"roll_type","hardness","roll_gsm","bopp","roll_color","roll_length","net_weight","gross_weight"];
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
                    $rowData["purchase_date"] = is_int($rowData["purchase_date"])? getDateColumnAttribute($rowData['purchase_date']) : $rowData['purchase_date'];
                }
                $rules = [
                    'vendor_name' => [
                        "required",
                        function($attribute, $value, $fail)use ($rowData,$index ){
                            if(!$this->_M_VendorDetail->where(DB::raw("UPPER(vendor_name)"),trim(strtoupper($value)))->first()){
                                $fail('The '.$attribute.' is invalid.');
                            }
                        },
                    ],
                    "quality"=>[
                        "required",
                        function ($attribute, $value, $fail)use ($rowData,$index )
                        {
                            $vendor = $this->_M_VendorDetail->where(DB::raw("upper(vendor_name)"),trim(strtoupper($rowData["vendor_name"])))->first();
                            $quality = $this->_M_RollQualityMaster->where("vendor_id",$vendor->id??0)->where(DB::raw("upper(quality)"),trim(strtoupper($value)))->first();
                            if($vendor && !$quality)
                            {
                                $fail('The '.$attribute.' is invalid.');
                            }

                        },
                    ],
                    'purchase_date' => 'required|date',
                    'roll_size' => 'required|numeric',
                    'roll_type' => 'nullable|in:NW,BOPP,LAM',
                    "hardness" => "nullable",
                    'roll_gsm' => 'required|numeric',
                    'bopp' => [
                        'required_if:roll_type,BOPP,LAM',
                        function ($attribute, $value, $fail)use ($rowData,$index )
                        {
                            $sumJson = $value ? array_sum(explode("/",$value)):null;
                            $gsm = $rowData["roll_gsm"];
                            if($sumJson && $sumJson!=$gsm)
                            {
                                $fail('The '.$attribute.' thickness is invalid.');
                            }
                            if($rowData["roll_type"]=="BOPP" && sizeof(explode("/",$value))!=3){
                                $fail('The '.$attribute.' thickness is invalid. like(GSM/lamination/BOPP)');
                            }
                            if($rowData["roll_type"]=="LAM" && sizeof(explode("/",$value))!=2){
                                $fail('The '.$attribute.' thickness is invalid. like(GSM/lamination)');
                            }

                        },

                    ],
                    'roll_color' => 
                    [
                       "required" ,
                       function($attribute, $value, $fail)use ($rowData,$index ){
                            $color = $this->_M_RollColor->where(DB::raw("UPPER(color)"),strtoupper(trim($value)))->first();
                            if($rowData["roll_size"]<=2){
                                $color = $this->_M_LoopStock->where(DB::raw("UPPER(loop_color)"),strtoupper(trim($value)))->first();
                            }
                            if(!$color){
                                $fail('The '.$attribute.' is invalid.');
                            }
                        },
                    ],
                    'roll_length' => 'required|int',
                    'net_weight' => 'required|numeric',
                    'gross_weight' => 'required|numeric',
                ];
                if(isset($rowData['roll_no'])){
                    $rules["roll_no"]=[
                        "nullable",
                        function($attribute, $value, $fail)use ($rowData,$index ){
                            $existsInTransit = DB::table('roll_transits')
                                ->where("roll_no", $value)
                                ->exists();

                            $existsInDetail = DB::table('roll_details')
                                ->where("roll_no", $value)
                                ->exists();

                            if ($existsInTransit || $existsInDetail) {
                                $fail('The '.$attribute.'['.$value.'] is already Exists.');
                            }


                        },
                    ];
                }
                $validator = Validator::make($rowData, $rules);

                if ($validator->fails()) {
                    $validationErrors[$index] = $validator->errors()->all();
                }
                $dataWithHeadings[] = $rowData; 
            }

            if($dataWithHeadings && isset($dataWithHeadings[0]["roll_no"])){
                $group = collect($dataWithHeadings)->groupBy("roll_no")->filter(function($val,$index){                    
                    return $index && $val->count()>1;
                });
                
                if($group->count()>0){
                    foreach($group as $index=>$val){                        
                        $validationErrors[] = ["Roll no $index is repeated ".sizeof($val)." time"];
                    }
                }

            }

            if (!empty($validationErrors)) {
                return responseMsgs(false,"Validation Error",$validationErrors);
            }

            // Import the CSV file using the RollDetailsImport class
            DB::beginTransaction();
            Excel::import(new RollDetailsImport, $file);
            DB::commit();
            return responseMsgs(true,"data import","");

        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    function rollBook(Request $request){
        try{

            $rule=[
                "rollId"=>"required|exists:".$this->_M_RollDetail->getTable().",id,lock_status,false",
                "bookingForClientId"=>"required|exists:".$this->_M_ClientDetails->getTable().",id,lock_status,false",
                "bookingEstimatedDespatchDate"=>"required|date",
                "bookingBagUnits"=>"required|in:Kg,Piece",
                "bookingBagTypeId"=>"required|exists:".$this->_M_BagType->getTable().",id",
                "bookingPrintingColor"=>"required|array",
                "bookingPrintingColor.*"=>"required",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }

            $request->merge([
                "clientDetailId"=>$request->bookingForClientId,
                "estimateDeliveryDate"=>$request->bookingEstimatedDespatchDate,
            ]);

            $roll = $this->_M_RollDetail->find($request->rollId);
            $roll->client_detail_id = $request->bookingForClientId;
            $roll->estimate_delivery_date = $request->bookingEstimatedDespatchDate;
            $roll->bag_type_id = $request->bookingBagTypeId;
            $roll->bag_unit = $request->bookingBagUnits;
            $roll->w = $request->w;
            $roll->l = $request->l;
            $roll->g = $request->g;
            $roll->printing_color = $request->bookingPrintingColor; 
            $roll->loop_color = $request->looColor;

            $newRequest = new Request($roll->toArray());

            DB::beginTransaction();
            $roll->update();

            $orderId = $this->_M_OrderPunches->store($request);
            $newRequest->merge(["order_id"=>$orderId,"roll_id"=>$roll->id]);
            $this->_M_OrderRollBagType->store($newRequest);

            DB::commit();
            return responseMsgs(true,"Roll No. ".$roll->roll_no." is Booked","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollList(Request $request){
        $flag= $request->flag;
        $user_type = Auth()->user()->user_type_id??"";
        if($request->ajax()){
            $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("
                                    roll_details.gsm_variation * 100 as gsm_variation,
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(roll_details.cutting_date, 'DD-MM-YYYY') as cutting_date ,    
                                    TO_CHAR(printing_schedule_details.printing_date, 'DD-MM-YYYY') as schedule_date_for_print ,
                                    TO_CHAR(cutting_schedule_details.cutting_date , 'DD-MM-YYYY') as schedule_date_for_cutting ,
                                    concat(roll_quality_masters.quality ,'  (',roll_details.hardness,')') as quality ,
                                    grade_masters.grade                              
                                    ")
                                )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->leftJoin("roll_quality_masters","roll_quality_masters.id","roll_details.quality_id")
                    ->join("grade_masters","grade_masters.id","roll_quality_masters.grade_id")
                    ->leftJoin("printing_schedule_details",function($join){
                        $join->on("printing_schedule_details.roll_id","=","roll_details.id")
                        ->where("printing_schedule_details.lock_status",false);
                    })
                    ->leftJoin("cutting_schedule_details",function($join){
                        $join->on("cutting_schedule_details.roll_id","=","roll_details.id")
                        ->where("cutting_schedule_details.lock_status",false);
                    })
                    ->where("roll_details.lock_status",false);  
            if($request->printedRollOnly){
                $data->where("roll_details.is_printed",true);
            }
            if($request->notBookedRollOnly){
                $data->whereNull("roll_details.client_detail_id");
            }                  
            if($flag!="register"){
                $data->where("roll_details.is_cut",false)
                    ->where("roll_details.is_roll_sell",false);
            }
            if($flag=="register" ){
                $fromDate = $request->fromDate;
                $uptoDate = $request->uptoDate;
                if($fromDate && $uptoDate){              
                    $data->whereBetween("purchase_date",[$fromDate,$uptoDate]);
                }
                elseif($fromDate){
                    $data->where("purchase_date",">=",$fromDate);
                }
                elseif($uptoDate){
                    $data->where("purchase_date","<=",$uptoDate);
                }
            }

            if($flag=="schedule-printing"){
                if(!in_array($user_type,[11,12])){
                    $data->whereNotNull("roll_details.client_detail_id");
                }
                $data->where("roll_details.is_printed",false)
                    ->where(function($where){
                        $where->where("roll_details.is_schedule_for_print",false)
                        ->orWhere("roll_details.schedule_date_for_print","<",Carbon::now()->format("Y-m-d"));
                    })
                    ->orderBy("roll_details.despatch_date","ASC");
            }elseif($flag=="print"){
                $data->where("roll_details.is_printed",false)
                ->where("roll_details.is_schedule_for_print",true)
                ->orderBy("roll_details.schedule_date_for_print","ASC");
            }elseif($flag=="schedule-cutting"){
                if(!in_array($user_type,[11,12])){
                    $data->whereNotNull("roll_details.client_detail_id");
                }
                $data->where("roll_details.is_cut",false)
                    ->where(function($where){
                        $where->where("roll_details.is_schedule_for_cutting",false)
                        ->orWhere("roll_details.schedule_date_for_cutting","<",Carbon::now()->format("Y-m-d"));
                    })
                    ->orderBy("roll_details.schedule_date_for_cutting","ASC");
            }elseif($flag=="cutting"){
                $data->where("roll_details.is_cut",false)
                ->where("roll_details.is_schedule_for_cutting",true)
                ->orderBy("roll_details.schedule_date_for_cutting","ASC");
            }
            else{
                $data->orderBy("roll_details.id","DESC");
            }
            
            if ($request->has('export')) {
                // Skip pagination when exporting
                $data = $data->get();
                return Excel::download(new ExportRoll($data), 'roll.xlsx');
            }
            // Handling search
            if ($request->has('search')) {
                $search = $request->search['value'];  // search term from DataTables
                
                $data = $data->where(function ($query) use ($search) {

                    $query->where("roll_details.roll_no","LIKE", "%$search%")
                        ->orWhere("roll_details.purchase_date","LIKE", "%$search%")
                        ->orWhere("roll_details.size","LIKE", "%$search%")
                        ->orWhere("roll_details.gsm","LIKE", "%$search%")
                        ->orWhere("roll_details.roll_color","LIKE", "%$search%")
                        ->orWhere("roll_details.length","LIKE", "%$search%")
                        ->orWhere("roll_details.net_weight","LIKE", "%$search%")
                        ->orWhere("roll_details.gross_weight","LIKE", "%$search%")
                        ->orWhere('vendor_detail_masters.vendor_name', 'LIKE', "%$search%")
                        ->orWhere('client_detail_masters.client_name', 'LIKE', "%$search%")
                        ->orWhere('bag_type_masters.bag_type', 'LIKE', "%$search%");  // Assuming ststop is a field to search
                });
            }
            $data = $data->get();
            $summary=[
                "totalWeight"=>roundFigure($data->sum("net_weight")),
            ];
            // DB::enableQueryLog();
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('row_color', function ($val) use($flag) {
                    $color = "";
                    $gsmVariationPer = $val->gsm_variation;
                    if(!is_between($gsmVariationPer,-8,8)){
                        $color="tr-gsm_variation_danger";
                    }
                    elseif(!is_between($gsmVariationPer,-4,4)){
                        $color="tr-gsm_variation";
                    }
                    if($val->for_client_id && $val->is_printed){
                        $color="tr-client-printed";
                    }elseif($val->is_printed){
                        $color="tr-printed";
                    }
                    elseif($val->for_client_id){
                        $color="tr-client";
                    }
                    if($flag=="schedule-printing"){
                        $color="";
                        if($flag=="schedule" && $val->estimated_despatch_date ){
                            $dayDiff = Carbon::now()->diffInDays(Carbon::parse($val->estimated_despatch_date),false);
                            
                            if($dayDiff<3){
                                $color="tr-primary-print";
                            }
                            if($dayDiff<2){
                                $color="tr-argent-print";
                            }
                            if($dayDiff<0){
                                $color="tr-expiry-print blink";
                            }
                        }
                    }
                    if($flag=="schedule-cutting"){
                        $color="";
                        if($flag=="schedule" && $val->estimated_despatch_date ){
                            $dayDiff = Carbon::now()->diffInDays(Carbon::parse($val->estimated_despatch_date),false);
                            
                            if($dayDiff<3){
                                $color="tr-primary-print";
                            }
                            if($dayDiff<2){
                                $color="tr-argent-print";
                            }
                            if($dayDiff<0){
                                $color="tr-expiry-print blink";
                            }
                        }
                    }
                    
                    return $color;
                })
                ->addColumn('gsm_variation', function ($val) {                        
                    return roundFigure($val->gsm_variation)."%";
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("bag_size",function ($val) {
                    return $val->bag_type_id ? ((float)$val->w." x ".(float)$val->l.($val->g?(" x ".(float)$val->g):"")):null;
                })
                ->addColumn('action', function ($val) use($flag,$user_type) {                    
                    $button = "";
                    if($val->is_cut || $val->is_roll_sell){
                        return $button;
                    } 
                    if(in_array($user_type,[1,2]) && (!$val->is_printed)){
                        $button .= '<button class="btn btn-sm btn-info" onClick="showConfirmDialog('."'Are sure to Delete??',function (){transferInTransit(".$val->id.');})" >Back In Transit</button>';
                    }                   
                    if(!($val->is_cut || $val->is_printed || $val->is_roll_sell)){
                        $button.='<button class="btn btn-sm btn-primary" onClick="editRoll('.$val->id.')" >Edit</button>';
                    }
                    if(!($val->is_cut || $val->is_roll_sell)){
                        $button.='<button class="btn btn-sm btn-secondary" onClick="splitRole('.$val->id.')" >Split</button>';
                    }
                    if(in_array($user_type,[1,2]) && !$val->client_detail_id){
                        $button .= '<button class="btn btn-sm btn-warning" onClick="openModelBookingModel('.$val->id.')" >Book</button>';
                    }if(in_array($user_type,[1,2]) && $val->client_detail_id && !$val->is_printed){
                        $button .= '<button class="btn btn-sm btn-danger" onClick="openModelBookingModel('.$val->id.')" >Alter Booking</button>';
                        $button .= '<button class="btn btn-sm btn-warning" onClick="removeBooking('.$val->id.')" >Remove</button>';
                    }
                    if($flag=="schedule-printing"){
                        $button='<button class="btn btn-sm btn-warning" onClick="openPrintingScheduleModel('.$val->id.')" >Schedule For Print</button>';
                        if($val->is_schedule_for_print){
                            $button='<button class="btn btn-sm btn-warning" onClick="openPrintingScheduleModel('.$val->id.')" >Re-Schedule For Print</button>';
                        }
                    }
                    if($flag=="print"){
                        $button='<button class="btn btn-sm btn-info" onClick="openPrintingModel('.$val->id.')" >Update Print</button>';
                    }
                    if($flag=="schedule-cutting"){
                        $button='<button class="btn btn-sm btn-warning" onClick="openCuttingScheduleModel('.$val->id.')" >Schedule For Cut</button>';
                        if($val->is_schedule_for_cutting){
                            $button='<button class="btn btn-sm btn-warning" onClick="openCuttingScheduleModel('.$val->id.')" >Re-Schedule For Cut</button>';
                        }
                    }
                    if($flag=="cutting"){
                        $button='<button class="btn btn-sm btn-info" onClick="openCuttingModel('.$val->id.')" >Update Cutting</button>';
                    }
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->with($summary)
                ->make(true);
            return $list;

        }
        $data["flag"]=$flag;
        return view("Roll/list",$data);
    }

    public function generateRollChalan(Request $request){
        try{          
            // return $pdf->output(); 
            $rolls = $this->_M_RollDetail->whereIn("id",collect($request->rolls)->pluck("id"))->get()->map(function($item){
                $item->quality = $item->getQualityType()->first()?->quality;
                return $item;
            });
            $parentTable=[];
            $rollTypeGroup = $rolls->groupBy(["roll_type"]);
            foreach($rollTypeGroup as $unit=>$bb){
                $qualityGroup = $bb->sortBy(["quality_id"]);
                $table=[];
                $table["grand_total"]=[
                    "total"=>$bb->count(),
                    "total_gross_weight"=> collect($bb)->sum("gross_weight") ,
                    "total_net_weight"=> collect($bb)->sum("net_weight") ,
                ];
                foreach($qualityGroup as $key=>$val){
                    $table["row"][]=$val;
                }
                $parentTable[$unit]=$table;
            }
            // dd($parentTable);
            
            $client = $this->_M_ClientDetails->find($request->saleClintId);
            $vendor = $this->_M_VendorDetail->find($request->vendorId);
            $auto = $this->_M_Auto->find($request->autoId);
            $transposer = $this->_M_Transporter->find($request->transporterId);
            $fyear=getFY();
            list($fromDate,$uptoDate) = explode("-",$fyear);
            $fromDate=$fromDate."-04-01"; 
            $uptoDate=$uptoDate."-03-31"; 
            $transPortStatus = $request->transportStatus??0;         
            $count = $this->getChalaneSequence($transPortStatus);
            $chalanNo="RS"."-";
            
            $key=$chalanNo;
            $chalanNo .=substr("O",0,1)."-";
            $chalanNo .=str_pad((string)$count,4,"0",STR_PAD_LEFT); 
            
            if($client && $client->id==1){
                $godownDtl = Config::get("customConfig.godownDtl");
                foreach($godownDtl as $key=>$val){
                    $client->$key=$val;
                }
            }
            if($client){
                $client->address = $request->address??$client->address;
            }if($vendor){
                $vendor->address = $request->address??$vendor->address;
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
            $data["vendor"] = $vendor;
            // dd($parentTable);
            $pdf = Pdf::loadView('pdf.rollSell', $data);
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
            return responseMsg(false,"Server Error","");
        }
    }

    public function sellRole(Request $request){
        try{
            $rules=[
                "saleClintId"   => "required_without:vendorId",
                "vendorId"      => "required_without:saleClintId",
                "dispatchedDate" => "required|date", // Ensures dispatchedDate is a valid date
                "invoiceNo" => "required", // Invoice number is mandatory
                "purpose" => "required|array",
                "rolls"=>"required|array",
                "rolls.*.id"=>"required|exists:".$this->_M_RollDetail->getConnectionName().".".$this->_M_RollDetail->getTable().",id,lock_status,false,is_cut,false,is_roll_sell,false",
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $request->merge(["transport_date"=>$request->dispatchedDate??Carbon::now()->format("d-m-Y")]);
            $request->merge(["clientId"=>$request->saleClintId]);
            $request->merge(["venderId"=>$request->vendorId]);
            DB::beginTransaction();
            $tranId = $this->_M_RollTransport->store($request);
            foreach($request->rolls as $index=> $val){
                $roll1 = $this->_M_RollDetail->where("id",$val["id"])->first();
                $removeVal=["client_detail_id","estimate_delivery_date","delivery_date","bag_type_id","bag_unit","w","l","g","loop_color"];
                foreach($removeVal as $column){
                    $roll1->$column = null;
                }
                $roll1->is_roll_sell =true;
                $newRequest = new Request($val);
                $newRequest->merge([
                    "roll_transport_id"=>$tranId,
                    "rollId"=>$roll1->id,
                ]);
                $this->_M_RollTransportDetail->store($newRequest);
                $order_filed=["client_detail_id","estimate_delivery_date","printing_color","loop_color","bag_type_id","bag_unit","w","l","g"];
                $avg1 = 0;
                $bag1 = $this->_M_BagType->find($roll1->bag_type_id);
                if($bag1){
                    $newRequest = new Request();
                    $newRequest->merge([
                        "formula"=>$bag1->roll_size_find,
                        "bookingBagUnits"=>"M",                    
                        // "gsm" => $request->bagGsm,
                        "bagL"=> $roll1->l,
                        "bagW"=> $roll1->w,
                        "bagG"=> $roll1->g,
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    

                    // possible production 

                    $bestFind = "";
                    $bestFind2 = ""; 
                    if($roll1->bag_unit=="Kg"){
                        $bestFind = "RW";
                        $bestFind2 = "RW";
                    }elseif($roll1->bag_unit=="Piece"){
                        $bestFind = $bag1->roll_find;                    
                        $bestFind2 = $bag1->roll_find_as_weight;
                    }

                    $newRequestOld = new Request($roll1->toArray());
                    $newRequestOld->merge([
                        "formula"=>$bestFind,
                        "bookingBagUnits"=>$roll1->bag_unit,
                        "length" => $roll1->length,
                        "netWeight" => $roll1->net_weight,
                        "size" => $roll1->size,
                        "gsm" => $roll1->gsm,
                        "bagL"=> $roll1->l,
                        "bagW"=> $roll1->w,
                        "bagG"=> $roll1->g,
                    ]);
                    $newRequestOld2 = new Request($newRequestOld->all());
                    $newRequestOld2->merge([
                        "formula"=>$bestFind2
                    ]);
                    $result = $this->calculatePossibleProduction($newRequestOld);
                    $result1 = $this->calculatePossibleProduction($newRequestOld2);
                    $avg1 = round((($result["result"]??0)+($result1["result"]??0))/2);
                }
                
                $order1=[];
                foreach($order_filed as $key){
                    $order1[$key]=$roll1[$key];
                }
                $orderRollBag1 = $this->_M_OrderRollBagType->where("roll_id",$roll1->id)->where("lock_status",false)->orderBy("id","DESC")->first();
                
                // remover from booking;  
                if($orderRollBag1){
                    $order1 = $this->_M_OrderPunches->find($orderRollBag1->order_id);
                    $order1->booked_units = $order1->booked_units - $avg1 ;
                    $order1->disbursed_units = 0;
                    $orderRollBag1->lock_status= true;
                    
                    $orderRollBag1->update();
                    $order1->update();
                } 
                $roll1->update();
            }
            DB::commit();
            return responseMsgs(true,"roll shall","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function splitRoll(Request $request){
        try{
            $roll = $this->_M_RollDetail->find($request->rollSplitId);
            $rules=[
                "rollSplitId"   => "required|exists:".$this->_M_RollDetail->getConnectionName().".".$this->_M_RollDetail->getTable().",id,lock_status,false,is_cut,false,is_roll_sell,false",
                "rollSplitNetWight"      => "required|numeric|min:0.1|max:".($roll ? $roll->net_weight:"0"),
                "rollSplitGrossWeight"      => "required|numeric|min:0.1|max:".($roll ? $roll->gross_weight:"0"),
                "rollSplitSize" => "required|numeric|min:0.1|max:".($roll ? $roll->size:"0"),
                "rollSplitLength" => "required|numeric|min:0.1|max:".($roll ? $roll->length:"0"),
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            
            $parentRoll = $roll;
            $orderRollBag = $this->_M_OrderRollBagType->where("roll_id",$roll->id)->where("lock_status",false)->orderBy("id","DESC")->first();
            $split_role_id = $roll->id;
            if($roll->split_role_id){
                $split_role_id = $roll->split_role_id;
                $parentRoll =  $this->_M_RollDetail->find($roll->split_role_id);
            }
            $rollNo =  generateSubRoll($parentRoll->roll_no);
            $rollNo2 =  generateSubRoll($parentRoll->roll_no,1);
                     
            
            DB::beginTransaction();  

            //copy Old Roll In rollTransit and then transfer It On roll Detail
            $copyOne = $roll->replicate();
            $copyOne->setTable($this->_M_RollTransit->getTable());
            $copyOne->roll_no = $rollNo;
            $copyOne->split_role_id = $parentRoll->id;
            $copyOne->save();

            $roll1 = $copyOne->replicate();
            $roll1->setTable($this->_M_RollDetail->getTable());
            $roll1->id = $copyOne->id;
            $roll1->save();

            $copyOne->delete();

            $copyTwo = $roll->replicate();
            $copyTwo->setTable($this->_M_RollTransit->getTable());
            $copyTwo->roll_no = $rollNo2;
            $copyTwo->net_weight = $request->rollSplitNetWight;
            $copyTwo->gross_weight = $request->rollSplitGrossWeight;
            $copyTwo->size = $request->rollSplitSize;
            $copyTwo->length = $request->rollSplitLength;
            $copyTwo->split_role_id = $parentRoll->id;
            $removeVal=["client_detail_id","estimate_delivery_date","delivery_date","bag_type_id","bag_unit","w","l","g","loop_color"];
            foreach($removeVal as $column){
                $copyTwo->$column = null;
            }
            $copyTwo->save();

            $roll2 = $copyTwo->replicate();
            $roll2->setTable($this->_M_RollDetail->getTable());
            $roll2->id = $copyTwo->id;
            $roll2->save();

            $copyTwo->delete();

            $avg1 = 0;
            $bag1 = $this->_M_BagType->find($roll->bag_type_id);
            if($bag1){
                $newRequest = new Request();
                $newRequest->merge([
                    "formula"=>$bag1->roll_size_find,
                    "bookingBagUnits"=>"M",                    
                    // "gsm" => $request->bagGsm,
                    "bagL"=> $roll->l,
                    "bagW"=> $roll->w,
                    "bagG"=> $roll->g,
                ]);
                $result = $this->calculatePossibleProduction($newRequest);
                

                // possible production 

                $bestFind = "";
                $bestFind2 = ""; 
                if($roll->bag_unit=="Kg"){
                    $bestFind = "RW";
                    $bestFind2 = "RW";
                }elseif($roll->bag_unit=="Piece"){
                    $bestFind = $bag1->roll_find;                    
                    $bestFind2 = $bag1->roll_find_as_weight;
                }

                $newRequestOld = new Request($roll2->toArray());
                $newRequestOld->merge([
                    "formula"=>$bestFind,
                    "bookingBagUnits"=>$roll1->bag_unit,
                    "length" => $roll2->length,
                    "netWeight" => $roll2->net_weight,
                    "size" => $roll2->size,
                    "gsm" => $roll2->gsm,
                    "bagL"=> $roll1->l,
                    "bagW"=> $roll1->w,
                    "bagG"=> $roll1->g,
                ]);
                $newRequestOld2 = new Request($newRequestOld->all());
                $newRequestOld2->merge([
                    "formula"=>$bestFind2
                ]);
                $result = $this->calculatePossibleProduction($newRequestOld);
                $result1 = $this->calculatePossibleProduction($newRequestOld2);
                $avg1 = round((($result["result"]??0)+($result1["result"]??0))/2);
            }
            // update from booking;  
            if($orderRollBag){
                $order1 = $this->_M_OrderPunches->find($orderRollBag->order_id);
                $order1->booked_units = $order1->booked_units - $avg1 ;
                $order1->disbursed_units = 0;
                
                // change roll_id
                $orderRollBag->roll_id = $roll1->id;
                $orderRollBag->update();
                // $order1->update();
            } 
            $roll->lock_status = true;
            $roll->update();
            // dd($order1,$avg1);
            DB::commit();
            return responseMsgs(true,"roll Split","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
    public function chalanRegister(Request $request){
        if($request->ajax()){
            $data = $this->_M_RollTransport->select("roll_transports.*","client_detail_masters.client_name",
                        "roll_transport_details.total_rolls","roll_transport_details.total_return_rolls",
                        DB::raw("
                        CASE WHEN client_detail_masters.id IS NULL THEN vendor_detail_masters.vendor_name ELSE client_detail_masters.client_name END AS client_name,
                        CASE WHEN auto_details.id IS NULL THEN transporter_details.transporter_name ELSE auto_details.auto_name END AS auto_name
                        "),
                    )
                    ->join(DB::raw("(
                            select roll_transport_id, count(roll_id) AS total_rolls, 
                                count(case when is_roll_return=true then roll_id else null end) AS total_return_rolls
                            from roll_transport_details
                            where roll_transport_details.lock_status = false
                            group by roll_transport_id
                            ) AS roll_transport_details"),"roll_transport_details.roll_transport_id","roll_transports.id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_transports.client_id")
                    ->leftJoin("vendor_detail_masters","vendor_detail_masters.id","roll_transports.vendor_id")
                    ->leftJoin("auto_details","auto_details.id","roll_transports.auto_id")
                    ->leftJoin("transporter_details","transporter_details.id","roll_transports.transporter_id")
                    ->where("roll_transports.lock_status",false)
                    ->orderBy("roll_transports.transport_date","DESC");

            if($request->fromDate && $request->uptoDate){
                $data->WhereBetween("roll_transports.transport_date",[$request->fromDate,$request->uptoDate]);
            }elseif($request->fromDate){
                $data->Where("roll_transports.transport_date",$request->fromDate);
            }elseif($request->uptoDate){
                $data->Where("roll_transports.transport_date",$request->uptoDate);
            }

            if($request->autoId){
                $data->where("roll_transports.auto_id",$request->autoId);
            }

            if($request->transporterId){
                $data->where("roll_transports.transporter_id",$request->transporterId);
            }
            if($request->billNo){
                $data->where("roll_transports.bill_no",$request->billNo);
            }
            if($request->invoiceNo){
                $data->where("roll_transports.invoice_no",$request->invoiceNo);
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
                ->addColumn('purpose', function ($val) { 
                    return $val->purpose ? json_decode($val->purpose) : [];
                })
                ->addColumn('transport_date', function ($val) { 
                    return $val->transport_date ? Carbon::parse($val->transport_date)->format("d-m-Y") : "";
                })
                ->make(true);
            return $list;
        }
        $data["autoList"] = $this->_M_Auto->getAutoListOrm()->orderBy("id","ASC")->get();
        $data["transporterList"] = $this->_M_Transporter->getAutoListOrm()->orderBy("id","ASC")->get();
        return view("Roll/chalanRegister",$data);
    }

    public function transPortDtlHtml($id,Request $request){
        try{
            $transport = $this->_M_RollTransport->find($id);
            $transportDtl = $this->_M_RollTransportDetail->where("roll_transport_id",$id)->where("lock_status",false)->get();
            $transport->total_roll = $transportDtl->count();
            $transport->total_return_roll = $transportDtl->where("is_roll_return",true)->count();
            $auto = $this->_M_Auto->find($transport->auto_id);
            $transporter = $this->_M_Transporter->find($transport->transporter_id);
            $transport->auto_name = $auto->auto_name??"";
            $transport->transporterLabel = $transporter ? ($transporter->is_bus ? "Bus Name : " : "Transporter Name : ") : "";
            $transport->transporter_name = $transporter->transporter_name??"";
            $transport->gstLabel = $transporter ? ($transporter->is_bus ? "Bus No : " : "GST No : ") : "";
            $transport->gst_no = $transporter->gst_no??"";


            $rolls = $this->_M_RollDetail->whereIn("id",$transportDtl->pluck("roll_id"))->get()->map(function($item)use($transportDtl){
                $item->is_roll_return = $transportDtl->where("roll_id",$item->id)->first()?->is_roll_return;
                $item->quality = $item->getQualityType()->first()?->quality;
                return $item;
            });
            $clientDtl = $this->_M_ClientDetails->find($transport->client_id);
            $vendorDtl = $this->_M_VendorDetail->find($transport->vendor_id);
            $data["transport"]=$transport;
            $data["transportDtl"]=$transportDtl;
            $data["auto"]=$auto;
            $data["transporter"]=$transporter;
            $data["clientDtl"]=$clientDtl;
            $data["vendorDtl"]=$vendorDtl;
            $data["rolls"]=$rolls;
            $html = view('Roll/transportDtl', $data)->render();
            return responseMsg(true,"html",$html);

        }catch(MyException $e){
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            return responseMsg(false,"Server Error","");
        }
    }

    public function sellRegister(Request $request){
        
        if($request->ajax())
        {
            $data = $this->_M_RollTransportDetail->select("roll_transport_details.*","roll_transports.transport_date","roll_transports.invoice_no","roll_transports.purpose",
                        "roll_transports.chalan_unique_id",
                        "roll_details.roll_no","roll_details.roll_type","roll_details.roll_color","roll_details.gsm",
                        "roll_details.gsm_json","roll_details.size","roll_details.net_weight","roll_details.gross_weight","roll_details.hardness","roll_details.length",
                        "roll_quality_masters.quality","grade_masters.grade",
                        DB::raw("
                        CASE WHEN client_detail_masters.id IS NULL THEN vendor_detail_masters.vendor_name ELSE client_detail_masters.client_name END AS client_name,
                        CASE WHEN auto_details.id IS NULL THEN transporter_details.transporter_name ELSE auto_details.auto_name END AS auto_name
                        "),
                    )
                    ->join("roll_transports","roll_transports.id","roll_transport_details.roll_transport_id")
                    ->join("roll_details","roll_details.id","roll_transport_details.roll_id")
                    ->leftJoin("roll_quality_masters","roll_quality_masters.id","roll_details.quality_id")
                    ->join("grade_masters","grade_masters.id","roll_quality_masters.grade_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_transports.client_id")
                    ->leftJoin("vendor_detail_masters","vendor_detail_masters.id","roll_transports.vendor_id")
                    ->leftJoin("auto_details","auto_details.id","roll_transports.auto_id")
                    ->leftJoin("transporter_details","transporter_details.id","roll_transports.transporter_id")
                    ->where("roll_transport_details.lock_status",false)
                    ->where("roll_transports.lock_status",false)
                    ->orderBy("roll_transports.transport_date","DESC")
                    ->orderBy("roll_transports.chalan_unique_id");

            if($request->fromDate && $request->uptoDate){
                $data->WhereBetween("roll_transports.transport_date",[$request->fromDate,$request->uptoDate]);
            }elseif($request->fromDate){
                $data->Where("roll_transports.transport_date",$request->fromDate);
            }elseif($request->uptoDate){
                $data->Where("roll_transports.transport_date",$request->uptoDate);
            }

            if($request->autoId){
                $data->where("roll_transports.auto_id",$request->autoId);
            }

            if($request->transporterId){
                $data->where("roll_transports.transporter_id",$request->transporterId);
            }
            if($request->billNo){
                $data->where("roll_transports.bill_no",$request->billNo);
            }
            if($request->invoiceNo){
                $data->where("roll_transports.invoice_no",$request->invoiceNo);
            }
            
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('transport_date', function ($val) { 
                    return $val->transport_date ? Carbon::parse($val->transport_date)->format("d-m-Y") : "";
                })
                ->addColumn('purpose', function ($val) { 
                    return $val->purpose ? json_decode($val->purpose) : [];
                })
                ->addColumn('action', function ($val) {                   
                    $button='<button class="btn btn-sm btn-info" onClick="openPreviewChalanModel('."'".$val->chalan_unique_id."'".')" >View Chalan</button>';
                    if(in_array(Auth()->user()->user_type_id,[1,2])){
                        // $button.='<button class="btn btn-sm btn-danger" onclick="showConfirmDialog('."'Are you sure you want to deactivate this item?', function() { deleteTransPortDtl('$val->id'); })".'" >Delete</button>';
                    }
                    if(in_array(Auth()->user()->user_type_id,[1,2])){
                        if(!$val->is_roll_return){
                            $button.='<button class="btn btn-sm btn-danger" onclick="showConfirmDialog('."'Are you sure you want to Sell Return this item?', function() { sellRollBak('$val->id'); })".'" >Sell Return</button>';
                        }else{
                            $button.='<span class="btn btn-sm btn-warning">Roll Is Returned</span>';
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
        
        return view("Roll/roll_transport",$data);
    }

    public function returnSell($id,Request $request){
        try{
            
            $tranportDtl = $this->_M_RollTransportDetail->find($id);
            $roll = $this->_M_RollDetail->find($tranportDtl->roll_id);
            $tranportDtl->is_roll_return=true;
            $roll->is_roll_sell = false;
            DB::beginTransaction();  
            $roll->update();          
            $tranportDtl->update();
            DB::commit();
            return responseMsg(true,"Roll Return","");
        }catch(MyException $e){
            DB::rollBack();
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsg(false,"Server error!!!","");
        }
    }

    public function rollDtl($id,Request $request){
        try{
            $data = $this->_M_RollDetail->find($id);
            if($request->flag=="cutting"){
                $data->schedule_date = $data->getCuttingSchedule()->first();
            }elseif($request->flag=="printing"){
                $data->schedule_date = $data->getPrintingSchedule()->first();
            }
            return responseMsgs(true,"roll dtl fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollDtlFull($id,Request $request){
        try{
            $data = $this->_M_RollDetail->find($id);
            if(!$data){
                $data = $this->_M_RollTransit->find($id);
            }
            if(!$data){
                throw new Exception("no data find");
            }
            $data->printing_color = json_decode($data->printing_color,true)??"";
            $data->vender_name = $data->getVendor()->first()->vendor_name??"";
            return responseMsgs(true,"roll dtl fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }


    #====== roll register =========================
    public function rollRegister(Request $request){
        $flag= $request->flag;
        $user_type = Auth()->user()->user_type_id??"";
        list($from,$upto)=explode("-",getFY());
        $data["fromDate"] = $from."-04-01";
        $data["uptoDate"] = Carbon::now()->format("Y-m-d");
        if($request->ajax()){
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $data = $this->_M_RollDetail->select("roll_details.*",
                                "vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("printing_schedule_details.printing_date AS schedule_date_for_print , 
                                        cutting_schedule_details.cutting_date AS schedule_date_for_cutting,
                                        printing_machine.name AS printing_machine_name,
                                        cutting_machine.name as cutting_machine_name,
                                        roll_quality_masters.quality"
                                ),
                                )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("machine_maters AS printing_machine","printing_machine.id","roll_details.printing_machine_id")
                    ->leftJoin("machine_maters AS cutting_machine","cutting_machine.id","roll_details.cutting_machine_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->leftJoin("roll_quality_masters","roll_quality_masters.id","roll_details.quality_id")
                    ->leftJoin("printing_schedule_details",function($join){
                        $join->on("printing_schedule_details.roll_id","=","roll_details.id")
                        ->where("printing_schedule_details.lock_status",false);
                    })
                    ->leftJoin("cutting_schedule_details",function($join){
                        $join->on("cutting_schedule_details.roll_id","=","roll_details.id")
                        ->where("cutting_schedule_details.lock_status",false);
                    })
                    ->where("roll_details.lock_status",false)
                    ->orderBy("roll_details.id","DESC"); 
            $loop = $this->_M_LoopDetail->select("loop_details.*","vendor_detail_masters.vendor_name",
                                                DB::raw("loop_color as roll_color , null as w , null as l , 
                                                null as g , null as bag_type , null as bag_unit , 
                                                null as client_name,
                                                null AS printing_machine_name,
                                                null as cutting_machine_name,
                                                roll_quality_masters.quality
                                                "))
                    ->join("vendor_detail_masters","vendor_detail_masters.id","loop_details.vender_id")
                    ->leftJoin("roll_quality_masters","roll_quality_masters.id","loop_details.quality_id")
                    ->where("loop_details.lock_status",false)
                    ->orderBy("loop_details.id","DESC"); 

            if($fromDate && $uptoDate){              
                $data->whereBetween("purchase_date",[$fromDate,$uptoDate]);
            }
            elseif($fromDate){
                $data->where("purchase_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("purchase_date","<=",$uptoDate);
            } 

            if($fromDate && $uptoDate){              
                $data->whereBetween("purchase_date",[$fromDate,$uptoDate]);
                $loop->whereBetween("purchase_date",[$fromDate,$uptoDate]);
            }
            elseif($fromDate){
                $data->where("purchase_date",">=",$fromDate);
                $loop->where("purchase_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("purchase_date","<=",$uptoDate);
                $loop->where("purchase_date","<=",$uptoDate);
            }
            $data = collect($data->get())->merge($loop->get());
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('row_color', function ($val) use($flag) {
                    $color = "";                    
                    $gsmVariationPer = $val->gsm_variation;
                    if(!is_between($gsmVariationPer,-8,8)){
                        $color="tr-gsm_variation_danger";
                    }
                    elseif(!is_between($gsmVariationPer,-4,4)){
                        $color="tr-gsm_variation";
                    }
                    if($val->for_client_id && $val->is_printed){
                        $color="tr-client-printed";
                    }elseif($val->is_printed){
                        $color="tr-printed";
                    }
                    elseif($val->for_client_id){
                        $color="tr-client";
                    }                    
                    return $color;
                })
                ->addColumn('purchase_date', function ($val) {                    
                    return $val->purchase_date ? Carbon::parse($val->purchase_date)->format("d-m-Y"):"";
                })
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn('size', function ($val) {                    
                    return $val->size<=2?"loop":$val->size;
                })
                ->addColumn("loop_color",function($val){
                    return"";
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
                // dd(DB::getQueryLog());
            return $list;

        }
        $data["flag"]=$flag;
        return view("Roll/register",$data);
    }

    public function loopRegister(Request $request){
        list($from,$upto)=explode("-",getFY());
        $data["fromDate"] = $from."-04-01";
        $data["uptoDate"] = Carbon::now()->format("Y-m-d");
        if($request->ajax()){
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $data = $this->_M_LoopDetail->select("loop_details.*","vendor_detail_masters.vendor_name",)
                    ->join("vendor_detail_masters","vendor_detail_masters.id","loop_details.vender_id")
                    ->where("loop_details.lock_status",false)
                    ->orderBy("loop_details.id","DESC"); 

            if($fromDate && $uptoDate){              
                $data->whereBetween("purchase_date",[$fromDate,$uptoDate]);
            }
            elseif($fromDate){
                $data->where("purchase_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("purchase_date","<=",$uptoDate);
            } 
            $list = DataTables::of($data)
                ->addIndexColumn() 
                ->addColumn("purchase_date",function($val){
                    return Carbon::parse($val->purchase_date)->format("d-m-Y");                        
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        return view("Roll/loopRegister",$data);
    }

    public function rollRegisterPrinting(Request $request){
        $flag= $request->flag;
        $machineId = $request->machineId;
        $user_type = Auth()->user()->user_type_id??"";
        list($from,$upto)=explode("-",getFY());        
        if($request->ajax()){
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(roll_details.cutting_date, 'DD-MM-YYYY') as cutting_date                                     
                                    ")
                                )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")                    
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.printing_machine_id",$machineId)
                    ->orderBy("roll_details.printing_date","DESC"); 

            if($fromDate && $uptoDate){              
                $data->whereBetween("printing_date",[$fromDate,$uptoDate]);
            }
            elseif($fromDate){
                $data->where("printing_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("printing_date","<=",$uptoDate);
            } 
            DB::enableQueryLog();
            // $data->get();
            // dd(DB::getQueryLog());
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('row_color', function ($val) use($flag) {
                    $color = "";
                    if($val->for_client_id && $val->is_printed){
                        $color="tr-client-printed";
                    }elseif($val->is_printed){
                        $color="tr-printed";
                    }
                    elseif($val->for_client_id){
                        $color="tr-client";
                    }                    
                    return $color;
                })
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("loop_color",function($val){
                    return"";
                })
                ->addColumn('bag_size', function ($val) { 
                    return (float)$val->w." x ".(float)$val->l.($val->g ?(" x ".(float)$val->g) :"") ;
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->addColumn("action",function($val){
                    $user = Auth::user();
                    $action="";
                    if(in_array($user->user_type_id,[1,2]) && !$val->is_cut){
                        $action="<button class='btn btn-sm btn-warning' onclick='deletePrintingConform(".$val->id.")'>Delete</button>";
                        $action.="<button class='btn btn-sm btn-success' onclick='editPrinting(".$val->id.",".$val->weight_after_print.")'>Edit</button>";
                    }
                    return $action;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        $data=[];
        $data["fromDate"] = $from."-04-01";
        $data["uptoDate"] = Carbon::now()->format("Y-m-d");
        $data["machine"] = $this->_M_Machine->find($machineId);
        return view("Roll/rollRegisterPrinting",$data);
    }

    public function editPrintingWeight(Request $request){
        try{
            $user = Auth()->user();
            $rules=[
                "rollId"=>"required|exists:".$this->_M_RollDetail->getTable().",id,is_printed,true",
                "printingWeight"=>"required",
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            if(!in_array($user->user_type_id,[1,2])){
                throw new MyException("Access Denial");
            }
            $roll = $this->_M_RollDetail->find($request->rollId);
            $roll->weight_after_print  = $request->printingWeight;
            $roll->update();
            return responseMsgs(true,"Update Printing Weight","");
        }catch(MyException $e){
            return responseMsgs(false,$e->getMessage(),"");
        }catch(Exception $e){
            return responseMsgs(false,"Server Error","");
        }
    }

    public function rollRegisterCutting(Request $request){
        $flag= $request->flag;
        $machineId = $request->machineId;
        $user_type = Auth()->user()->user_type_id??"";
        list($from,$upto)=explode("-",getFY());
        if($request->ajax()){
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                "garbage_accept_registers.id AS garbage_id","garbage_accept_registers.shift",
                                "operator.name as operator_name","helper.name as helper_name",
                                DB::raw("
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(roll_details.cutting_date, 'DD-MM-YYYY') as cutting_date                                     
                                    ")
                                )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id") 
                    ->leftJoin("garbage_accept_registers",function($join){
                        $join->on("garbage_accept_registers.roll_id","roll_details.id")
                        ->where('garbage_accept_registers.lock_status',false);
                    }) 
                    ->leftJoin("users AS operator","operator.id","garbage_accept_registers.operator_id") 
                    ->leftJoin("users AS helper","helper.id","garbage_accept_registers.helper_id")                    
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.cutting_machine_id",$machineId)
                    ->orderBy("roll_details.cutting_date","DESC"); 

            if($fromDate && $uptoDate){              
                $data->whereBetween("cutting_date",[$fromDate,$uptoDate]);
            }
            elseif($fromDate){
                $data->where("cutting_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("cutting_date","<=",$uptoDate);
            } 
            DB::enableQueryLog();
            // $data->get();
            // dd(DB::getQueryLog());
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('row_color', function ($val) use($flag) {
                    $color = "";
                    if($val->for_client_id && $val->is_printed){
                        $color="tr-client-printed";
                    }elseif($val->is_printed){
                        $color="tr-printed";
                    }
                    elseif($val->for_client_id){
                        $color="tr-client";
                    }                    
                    return $color;
                })
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("possible_piece",function($val){
                    $bag = $this->_M_BagType->find($val->bag_type_id);
                    $bestFind = "";
                    $bestFind2 ="";
                    $bestFind = $bag->roll_find;
                    $bestFind2 = $bag->roll_find_as_weight;
                    $newRequest = new Request();
                    $newRequest->merge(
                        [
                        "bookingBagUnits" => "Piece",
                        "formula" => $bestFind,
                        "length" => $val->length,
                        "netWeight"=>$val->net_weight,
                        "size"=>$val->size,
                        "gsm"=>$val->gsm,

                        "bagL"=>$val->l,
                        "bagW"=>$val->w,
                        "bagG"=>$val->g
                        ]
                    );
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula"=>$bestFind2
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    $result2 = $this->calculatePossibleProduction($newRequest2);
                    $qty = ((($result["result"]??0)+($result2["result"]??0))/2);
                    return round($qty);
                })
                ->addColumn('bag_size', function ($val) { 
                    return (float)$val->w." x ".(float)$val->l.($val->g ?(" x ".(float)$val->g) :"") ;
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
                // dd(DB::getQueryLog());
            return $list;

        }
        $data=[];
        $data["fromDate"] = $from."-04-01";
        $data["uptoDate"] = Carbon::now()->format("Y-m-d");
        $data["machine"] = $this->_M_Machine->find($machineId);
        return view("Roll/rollRegisterCutting",$data);
    }
    

    #===== roll schedule =========================

    public function rollSchedule(Request $request){
        try{

            $flag= $request->flag;
            $user_type = Auth()->user()->user_type_id??"";
            $currentDate = Carbon::now()->format("Y-m-d");
            
            if($request->ajax()){
                $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                    "client_detail_masters.client_name",
                                    "bag_type_masters.bag_type",
                                    DB::raw("
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(printing_schedule_details.printing_date, 'DD-MM-YYYY') as schedule_date_for_print ,
                                    TO_CHAR(cutting_schedule_details.cutting_date, 'DD-MM-YYYY') as schedule_date_for_cutting                                      
                                    ")
                                    )
                        ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                        ->Join("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                        ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                        ->leftJoin("printing_schedule_details",function($join){
                            $join->on("printing_schedule_details.roll_id","=","roll_details.id")
                            ->where("printing_schedule_details.lock_status",false);
                        })
                        ->leftJoin("cutting_schedule_details",function($join){
                            $join->on("cutting_schedule_details.roll_id","=","roll_details.id")
                            ->where("cutting_schedule_details.lock_status",false);
                        })
                        ->where("roll_details.lock_status",false)                        
                        ->where("roll_details.is_roll_sell",false);
                if($flag=="printing"){
                    $data->where("roll_details.is_printed",false)
                    ->whereNotNull(DB::raw("json_array_length(roll_details.printing_color)"))
                    ->where(function($where)use($currentDate){
                        $where->where("printing_schedule_details.printing_date","<",$currentDate)
                        ->orWhereNull("printing_schedule_details.id");

                    });
                } 

                if($flag=="cutting"){
                    $data->where("roll_details.is_cut",false)
                    ->where(function($where)use($currentDate){
                        $where->where("cutting_schedule_details.cutting_date","<",$currentDate)
                        ->orWhereNull("cutting_schedule_details.id");

                    })
                    ->where(function($where){
                        $where->whereNull("printing_schedule_details.id")
                            ->orWhere(DB::raw(" CASE WHEN printing_schedule_details.id IS NOT NULL AND roll_details.is_printed = TRUE THEN TRUE ELSE FALSE END"),true);
                    })
                    ->whereNotNull("roll_details.client_detail_id");
                }

                $data->orderBy("roll_details.estimate_delivery_date","ASC");
                // DB::enableQueryLog();
                // $data->get();
                // dd(DB::getQueryLog());
                
                $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('row_color', function ($val) use($flag) {
                        $color = "";
                        if($val->for_client_id && $val->is_printed){
                            $color="tr-client-printed";
                        }elseif($val->is_printed){
                            $color="tr-printed";
                        }
                        elseif($val->for_client_id){
                            $color="tr-client";
                        }                    
                        return $color;
                    })
                    ->addColumn('action', function ($val)use($flag) {                    
                        $button = "";
                        if($val->is_roll_cut){
                            return $button;
                        }
                        if($flag=="printing"){
                            if(!$val->schedule_date_for_print){
                                $button .= '<button class="btn btn-sm btn-warning" onClick="openPrintingScheduleModel('.$val->id.')" >Schedule</button>';
                            }elseif($val->schedule_date_for_print){
                                $button .= '<button class="btn btn-sm btn-danger" onClick="openPrintingScheduleModel('.$val->id.')" >Re-Schedule</button>';
                            }
                        }elseif($flag=="cutting"){
                            if(!$val->schedule_date_for_cutting){
                                $button .= '<button class="btn btn-sm btn-warning" onClick="openCuttingScheduleModel('.$val->id.')" >Schedule</button>';
                            }elseif($val->schedule_date_for_cutting){
                                $button .= '<button class="btn btn-sm btn-danger" onClick="openCuttingScheduleModel('.$val->id.')" >Re-Schedule</button>';
                            }
                        }
                        return $button;
                    })
                    ->addColumn('print_color', function ($val) {                    
                        return collect(json_decode($val->printing_color,true))->implode(",");
                    })
                    ->addColumn("loop_color",function($val){
                        return"";
                    })
                    ->addColumn("gsm_json",function($val){
                        return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                    })
                    ->rawColumns(['row_color', 'action'])
                    ->make(true);
                    // dd(DB::getQueryLog());
                return $list;
    
            }
            $data["flag"]=$flag;
            return view("Roll/schedule",$data);
        }catch(Exception $e){

        }
    }

    public function rollScheduleSet(Request $request){
        try{
            $flag =  $request->flag;
            $rules = [
                "scheduleDate" => "required|date|after_or_equal:" . Carbon::now()->format("Y-m-d"),
                "rolls" => "required|array",
                "rolls.*.id" => "required|exists:" . $this->_M_RollDetail->getTable() . ",id",
                "rolls.*.position" => "required|integer", // Use `integer` for clarity instead of `int`
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            DB::beginTransaction();
            foreach($request->rolls as $roll){
                $newRequest = new Request($roll);
                $newRequest->merge([
                    "roll_id"=>$roll["id"],
                    "cutting_date"=>$request->scheduleDate,
                    "printing_date"=>$request->scheduleDate,
                    "sl"=> $roll["position"],
                ]);
                if($flag=="printing"){
                    $this->_M_PrintingScheduleDetail->where("roll_id",$newRequest->roll_id)->update(["lock_status"=>true]);
                    $this->_M_PrintingScheduleDetail->store($newRequest);
                }elseif($flag=="cutting"){
                    $this->_M_CuttingScheduleDetail->where("roll_id",$newRequest->roll_id)->update(["lock_status"=>true]);
                    $this->_M_CuttingScheduleDetail->store($newRequest);
                }
            }
            DB::commit();
            return responseMsgs(true,"Roll Schedule for ".$flag,"");

        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollProduction(Request $request){
        $user_type = Auth()->user()->user_type_id??"";
        $machineId = $request->machineId;
        if($request->ajax())
        {
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            if(!$fromDate){
                $fromDate = Carbon::now()->format("Y-m-d");
            }
            if(!$uptoDate){
                $uptoDate = Carbon::now()->format("Y-m-d");
            }
            $data = $this->_M_RollDetail->select(
                                "roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(printing_schedule_details.printing_date, 'DD-MM-YYYY') as schedule_date_for_print                                      
                                    ")
                    )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->Join("printing_schedule_details",function($join){
                        $join->on("printing_schedule_details.roll_id","=","roll_details.id")
                        ->where("printing_schedule_details.lock_status",false);
                    })
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.is_roll_sell",false)
                    ->where("roll_details.is_printed",false)
                    ->orderBy("printing_schedule_details.sl","ASC");
            if($machineId==1){                
                $data->where(function($where){
                    $where->where(DB::raw("json_array_length(roll_details.printing_color)"),"<=",2)
                        ->orWhereNull("roll_details.printing_color");
                });
            }                     

            if($fromDate && $uptoDate){             
                $data->whereBetween("printing_schedule_details.printing_date",[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where("printing_schedule_details.printing_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("printing_schedule_details.printing_date","<=",$uptoDate);
            } 
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("loop_color",function($val){
                    return"";
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->addColumn('action', function ($val){                    
                    $button = "";
                    if(!$val->is_cut){
                        $button .= '<button class="btn btn-sm btn-warning" onClick="openPrintingUpdateModel('.$val->id.')" >Update</button>';
                    }                    
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        $data=[];
        $data["machine"] = $this->_M_Machine->find($machineId);
        $data["operator"] = $this->_M_User->getOperateList();
        $data["helper"] = $this->_M_User->getHelperList();
        $data["privDate"] = Carbon::now()->subDay()->format("Y-m-d");
        return view("Roll/rollProduction",$data);
    }

    public function rollProductionCutting(Request $request){
        $user_type = Auth()->user()->user_type_id??"";
        $machineId = $request->machineId;
        if($request->ajax())
        {
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(cutting_schedule_details.cutting_date, 'DD-MM-YYYY') as schedule_date_for_cutting                                      
                                    ")
                                )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")                    
                    ->Join("cutting_schedule_details",function($join){
                        $join->on("cutting_schedule_details.roll_id","=","roll_details.id")
                        ->where("cutting_schedule_details.lock_status",false);
                    })
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.is_cut",false)
                    ->where("roll_details.is_roll_sell",false)
                    ->orderBy("cutting_schedule_details.sl","ASC");                     

            if($fromDate && $uptoDate){             
                $data->whereBetween("cutting_schedule_details.cutting_date",[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where("cutting_schedule_details.cutting_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("cutting_schedule_details.cutting_date","<=",$uptoDate);
            } 

            if($machineId==1){                
                $data->where(function($where){
                    $where->where("roll_details.bag_type_id",5);
                });
            }  
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("loop_color",function($val){
                    return"";
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->addColumn('action', function ($val){                    
                    $button = "";
                    if(!$val->is_cut){
                        $button .= '<button class="btn btn-sm btn-warning" onClick="openCuttingUpdateModel('.$val->id.')" >Update</button>';
                    }                    
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        $data=[];
        $data["machine"] = $this->_M_Machine->find($machineId);
        $data["operator"] = $this->_M_User->getOperateList();
        $data["helper"] = $this->_M_User->getHelperList();
        $data["privDate"] = Carbon::now()->subDay()->format("Y-m-d");
        return view("Roll/rollProductionCutting",$data);
    }

    public function rollSearchPrinting(Request $request){
        try{
            $machineId = $request->machineId;
            $machine = $this->_M_Machine->find($machineId);
            $data = $this->rollSearchPrintingOrm()
                    ->where("roll_details.roll_no",$request->rollNo)
                    ->first();
            $message="Data Fetch";
            if($data && !$data->printing_color && $data->roll_type!="BOPP"){
                $message="Printing is not applicable on this roll";
                $data=null;
            }
            if($data && $machineId==1 && collect(json_decode($data->printing_color,true))->count()>2 && $data->roll_type!="BOPP"){
                $message=collect(json_decode($data->printing_color,true))->count()." Colors are not printed on this machine.";
                $data=null; 
            }
            if($data){
                $data->printing_color = collect(json_decode($data->printing_color,true))->implode(",");
                $data->bag_type = $data->getBagType()->first()->bag_type??"";
                $data->bag_size = (float)$data->w." x ".(float)$data->l.($data->g ?(" x ".(float)$data->g) :"");
            }
            return responseMsgs(true,$message,$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollSearchPrintingRollList(Request $request){
        try{
            $machineId = $request->machineId;
            $data = $this->rollSearchPrintingOrm()
                    // ->whereNotNull("roll_details.printing_color")                    
                    ->where(function($where) {
                        $where->whereNotNull("roll_details.printing_color")
                            ->orWhere("roll_details.roll_type", "BOPP");
                    })

                    ->where("roll_details.roll_no","ILIKE",'%'.$request->rollNo.'%');
            if($request->rollId){
                $data->whereNotIn("roll_details.id",$request->rollId);
            }
            $data = $data->get();
            $data=collect($data);
            if($machineId==1){
                $data = $data->filter(function($val){
                    return collect(json_decode($val->printing_color,true))->count()<=2;
                })->values();
                $data = collect($data);
            }
            return responseMsgs(true,"suggested Roll",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollSearchCutting(Request $request){
        try{
            $data = $this->rollSearchCuttingOrm()
                    ->where("roll_details.roll_no",$request->rollNo)                    
                    ->first();
            if($data){
                $data->printing_color = collect(json_decode($data->printing_color,true))->implode(",");
                $data->operator = $this->_M_User->getOperateList();
                $data->helper = $this->_M_User->getHelperList();
            }
            $message = "Data Fetch";
            if(!$data){
                $roll = $this->_M_RollDetail->where("roll_details.roll_no",$request->rollNo)->first();
                if(!$roll){
                    $message = "Roll is not Fined";
                }
                elseif(!$roll->is_printed && sizeof(json_decode($roll->printing_color,true))>0){
                    $message = "Roll is not Printed";
                }elseif($roll->is_cut){
                    $message = "Roll Already Cut";
                }
            }
            $data->bag_type = $data->getBagType()->first()->bag_type??"";
            $data->bag_size = (float)$data->w." x ".(float)$data->l.($data->g ?(" x ".(float)$data->g) :"");
            return responseMsgs(true,$message,$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollSearchCuttingRollList(Request $request){
        try{
            $data = $this->rollSearchCuttingOrm()
                    ->where("roll_details.roll_no","ILIKE","%".$request->rollNo."%");  
            if($request->rollId){
                $data->whereNotIn("roll_details.id",$request->rollId);
            }                  
            $data = $data->get();
                    
            return responseMsgs(true,"suggested Roll",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function getDataForCuttingGarbage(Request $request){
        try{
            $rolls = $this->_M_RollDetail->select("*")
                    ->whereIn("id",$request->rollIds)
                    ->get()
                    ->map(function($val){DB::enableQueryLog();
                        $orderRollBag = $val->getOrderRollBagType()->first();
                        $val->order_id = $orderRollBag->order_id??"";
                        $val->client_name = $val->getClient()->first()->client_name??"";
                        $val->bag_size = (float)$val->w." x ".(float)$val->l.($val->g ?(" x ".(float)$val->g) :"");
                        return $val;
                    });
            $orderIds = $rolls->pluck("order_id")->unique();
            $data=[];
            foreach($orderIds as $val){
                $roll = $rolls->where("order_id",$val)->map(function($item){
                    $item->weight = $item->weight_after_print ? $item->weight_after_print : $item->net_weight;
                    return $item;
                });
                $client_name = $roll->pluck("client_name")->unique();
                $bag_size = $roll->pluck("bag_size")->unique();
                $data[]=[
                    "order_id"=>$val,
                    "client_name"=>$client_name,
                    "roll_ids"=>$roll->pluck("id")->toArray(),
                    "total_weight"=>$roll->sum("weight") ,
                    "bag_size"=>$bag_size,
                ];
            }
            return responseMsgs(true,"modal Data",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollPrintingUpdate(Request $request){        
        try{
           
            $rule = [
                "id" => "required|exists:" . $this->_M_Machine->getTable() . ",id,is_printing,true",
                "printingUpdate" => "required|date|date_format:Y-m-d|before_or_equal:" . Carbon::now()->format("Y-m-d"),
                "shift" => "required|in:Day,Night",
                "operatorId" => "required|exists:".$this->_M_User->getTable().",id",
                "helperId" => "required|exists:".$this->_M_User->getTable().",id",
                "roll" => "required|array",
                "roll.*.id"=>"required|exists:".$this->_M_RollDetail->getTable().",id,lock_status,false,is_roll_sell,false,is_printed,false,is_cut,false",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }
            DB::beginTransaction();
            if($request->roll){
                $newRequest = new Request($request->all());
                $newRequest->merge(["printingDate"=>$request->printingUpdate,"user_id"=>Auth()->user()->id]);
                $entryId = $this->_M_PrintingEntry->store($newRequest);
            }
            foreach($request->roll as $index=>$val){
                $roll = $this->_M_RollDetail->find($val['id']);
                $roll->is_printed = true;
                $roll->printing_date = $request->printingUpdate;
                $roll->weight_after_print = $val["printingWeight"];
                $roll->printing_machine_id = $request->id;
                $roll->update();
                $colorArr=[];
                foreach($val["color"] as $index=>$ratio){
                    $colorArr[]=[
                        "color"=>$val["colorName"][$index],
                        "ratio"=>$ratio
                    ];

                }
                $newRequest = new Request(["printingId"=>$entryId,"rollId"=>$roll->id,"printing_color_json"=>$colorArr?$colorArr:null]);
                $this->_M_PrintingRegister->store($newRequest);
            }
            DB::commit();
            return responseMsgs(true,"Roll No ".$roll->roll_no." Printed","");

        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollPrintingDelete(Request $request){
        try{
            $roll = $this->_M_RollDetail->find($request->id);
            if($roll->is_cut){
                throw new MyException("Roll is cut. Please remove it first.");
            }
            $updateCutting = ["is_printed"=>false,"printing_date"=>null,"weight_after_print"=>null,"printing_machine_id"=>null];
            foreach($updateCutting as $key => $val){
                $roll->$key = $val;
            }
            $printingRegisterDtl = $this->_M_PrintingRegister->where("roll_id",$roll->id)->where("lock_status",false)->first();
            if($printingRegisterDtl){
                $printingRegisterDtl->lock_status = true;
                $printingResistor = $this->_M_PrintingEntry->find($printingRegisterDtl->printing_id);
                if($this->_M_PrintingRegister->where("id","<>",$printingRegisterDtl->id)->where("printing_id",$printingRegisterDtl->printing_id)->where("lock_status",false)->count()){
                    $printingResistor->lock_status = true;
                }
            }
            
            DB::beginTransaction();
            $roll->update();
            if($printingRegisterDtl){
                $printingRegisterDtl->update();
                $printingResistor->update();
            }
            DB::commit();
            return responseMsg(true,"Roll Printing Deleted","");
        }catch(MyException $e){
            DB::rollBook();
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            DB::rollBook();
            return responseMsg(false,"Server Error!!!","");
        }
    }

    public function rollCuttingUpdate(Request $request){        
        try{
           
            $rule = [
                "id" => "required|exists:" . $this->_M_Machine->getTable() . ",id,is_cutting,true",
                "cuttingUpdate" => "required|date|date_format:Y-m-d|before_or_equal:" . Carbon::now()->format("Y-m-d"),
                "shift" => "required|in:Day,Night",
                "operatorId" => "required|exists:".$this->_M_User->getTable().",id",
                "helperId" => "required|exists:".$this->_M_User->getTable().",id",
                "roll" => "required|array",
                "roll.*.id"=>"required|exists:".$this->_M_RollDetail->getTable().",id,lock_status,false,is_roll_sell,false,is_cut,false",
                // "roll.*.totalQtr"=>"required",
                "client.*.orderId"=>"required",
                "client.*.rollId.*.id"=>"required",
                "client.*.garbage"=>"required|numeric"
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }
            
            DB::beginTransaction();
            foreach($request->client as $val){
                $newRequest = new Request();
                $rolls = $this->_M_RollDetail->select("*")
                    ->whereIn("id",collect($val["rollId"])->pluck("id")->toArray())
                    ->get()->map(function($item){
                        $item->weight = $item->weight_after_print ? $item->weight_after_print : $item->net_weight;
                        return $item;
                    });
                $garbagePercent = (($val["garbage"]/$rolls->sum("weight"))*100);
                $clientId = ($rolls->pluck("client_detail_id")->unique())->first();
                $newRequest->merge([
                    "machine_id"=>$request->id,
                    "cutting_date"=>$request->cuttingUpdate,
                    "operator_id" => $request->operatorId,
                    "helper_id" => $request->helperId,
                    "shift" => $request->shift,
                    "client_id" => $clientId,
                    "order_id" => $val["orderId"],
                    "user_id" => Auth()->user()->id,
                    "garbage" => $val["garbage"],
                    "roll_weight"=>$rolls->sum("weight"),
                    "is_verify" => is_between($garbagePercent,-2,2) ? true : false,
                ]);
                $garbageId = $this->_M_GarbageEntry->store($newRequest);
                $averageGarbage = $val["garbage"]/$rolls->count();

                // deactivate all priv entry
                $this->_M_GarbageAcceptRegister->whereIn("roll_id",$rolls->pluck("id"))->update(["lock_status"=>true]);

                foreach($rolls as $index=>$val){
                    $roll = $this->_M_RollDetail->find($val->id);
                    $roll->is_cut = true;
                    $roll->cutting_date = $request->cuttingUpdate;
                    $roll->weight_after_cutting = ($val->weight - $averageGarbage)??0;
                    $roll->cutting_machine_id = $request->id;
                    $roll->update();
                    $newRequest = new Request($request->all());
                    $newRequest->merge([
                        "garbage_entry_id"=>$garbageId,
                        "roll_id"=>$roll->id,
                        "total_qtr"=>$averageGarbage??0,
                    ]);
                    $id = $this->_M_GarbageAcceptRegister->store($newRequest);
                }
            }
            DB::commit();
            return responseMsgs(true,"Cutting Roll Enter","");

        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }


    public function orderPunches(Request $request){
        $data["clientList"] = $this->_M_ClientDetails->getClientListOrm()->orderBy("client_name","ASC")->get();
        $data["bagType"] = $this->_M_BagType->getBagListOrm()->orderBy("id")->get();
        $data["color"] = $this->_M_Color->getColorListOrm()->orderBy("id")->get();
        $data["rollColor"]=$this->_M_RollColor->getRollColorListOrm()->orderBy("id")->get();
        $data["grade"]=$this->_M_GradeMaster->getGradeListOrm()->orderBy("id")->get();
        $data["fare"]=$this->_M_FareDetail->getFareListOrm()->orderBy("id")->get();
        $data["stereo"]=$this->_M_StereoDetail->getStereoListOrm()->orderBy("id")->get();
        $data["rateType"] = $this->_M_RateTypeMaster->getRateTypeListOrm()->orderBy("id")->get();
        $data["loopColor"] = $this->_M_LoopStock->getLoopColorOrm()->where("balance",">",0)->orderBy("id")->get();
        $data["broker"] = $this->_M_OrderBroker->getBrokerOrm()->orderBy("broker_name","ASC")->get();
        $rollStockGsm = $this->_M_RollDetail->select('gsm')->distinct()->pluck('gsm');
        $rollTransitGsm = $this->_M_RollTransit->select('gsm')->where("gsm",">",2)->distinct()->pluck('gsm');
        $data["gsm"] = $rollStockGsm->union($rollTransitGsm)->unique()->sort()->values();
        $data["altRollColor"]=$data["rollColor"];
        $data["altGsm"]=$data["gsm"];
        return view("Roll/orderPunches",$data);
    }

    public function alternateOptionsColor(Request $request){
        try{  
            if($request->bagQuality=="BOPP"){
                $request->merge(["bagGsm"=>array_sum(explode("/",$request->bagGsmJson))]);
            }         

            $roll= $this->getRollStockORM();
                
            $transit = $this->getRollTransitORM();
            if($request->bagQuality){
                $roll->where("roll_details.roll_type",$request->bagQuality);
                $transit->where("roll_transits.roll_type",$request->bagQuality);
            }
            if($request->gradeId){
                $quality = $this->_M_RollQualityMaster->where("grade_id",$request->gradeId)->get()->pluck("id");
                $roll->whereIn("roll_details.quality_id",$quality);
                $transit->whereIn("roll_transits.quality_id",$quality);
            }
            if($request->bookingBagColor){
                $bag_color = $request->bookingBagColor;
                if(!is_array($request->bookingBagColor))
                {
                    $bag_color=[$request->bookingBagColor];
                }
                $roll->whereNotIn("roll_details.roll_color",$bag_color);
                $transit->whereNotIn("roll_transits.roll_color",$bag_color);
            }
            if($request->bagGsm){
                $bagGsm = $request->bagGsm;
                if(!is_array($request->bagGsm))
                {
                    $bagGsm=[$request->bagGsm];
                }
                $bagGsm = array_map(function($val){
                    return (int) $val;
                },$bagGsm);
                $roll->whereNotIn("roll_details.gsm",$bagGsm);
                $transit->whereNotIn("roll_transits.gsm",$bagGsm);
            }
            
            $bag = $this->_M_BagType->find($request->bookingBagTypeId);
            if($bag){
                $newRequest = new Request();
                $newRequest->merge([
                    "formula"=>$bag->roll_size_find,
                    "bookingBagUnits"=>"M", 
                    "bagL"=> $request->l,
                    "bagW"=> $request->w,
                    "bagG"=> $request->g,
                ]);
                $result = $this->calculatePossibleProduction($newRequest);
                $fromSize = (int)($result["result"]??0);
                $uptoSize = $fromSize+3;
                $roll->whereBetween("roll_details.size",[$fromSize,$uptoSize]);
                $transit->whereBetween("roll_transits.size",[$fromSize,$uptoSize]);
            }
            $rolls = collect($roll->get())->union($transit->get());
            $data["altBagColor"] = collect($rolls->pluck("roll_color")->unique())->sort()->values();
            // $data["altGsm"] = collect($rolls->pluck("gsm")->unique())->sort()->values();
            return responseMsgs(true,"",$data);

        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function alternateOptionsGsm(Request $request){
        try{  
            if($request->bagQuality=="BOPP"){
                $request->merge(["bagGsm"=>array_sum(explode("/",$request->bagGsmJson))]);
            }         

            $roll= $this->getRollStockORM();
                
            $transit = $this->getRollTransitORM();
            if($request->bagQuality){
                $roll->where("roll_details.roll_type",$request->bagQuality);
                $transit->where("roll_transits.roll_type",$request->bagQuality);
            }
            if($request->gradeId){
                $quality = $this->_M_RollQualityMaster->where("grade_id",$request->gradeId)->get()->pluck("id");
                $roll->whereIn("roll_details.quality_id",$quality);
                $transit->whereIn("roll_transits.quality_id",$quality);
            }
            if($request->bookingBagColor){
                $bag_color = $request->bookingBagColor;
                if(!is_array($request->bookingBagColor))
                {
                    $bag_color=[$request->bookingBagColor];
                }
                if($request->altBagColor){
                    $bag_color = array_merge($bag_color,$request->altBagColor);
                }
                $roll->whereIn("roll_details.roll_color",$bag_color);
                $transit->whereIn("roll_transits.roll_color",$bag_color);
            }
            if($request->bagGsm){
                $bagGsm = $request->bagGsm;
                if(!is_array($request->bagGsm))
                {
                    $bagGsm=[$request->bagGsm];
                }
                $bagGsm = array_map(function($val){
                    return (int) $val;
                },$bagGsm);
                $roll->whereNotIn("roll_details.gsm",$bagGsm);
                $transit->whereNotIn("roll_transits.gsm",$bagGsm);
            }
            
            $bag = $this->_M_BagType->find($request->bookingBagTypeId);
            if($bag){
                $newRequest = new Request();
                $newRequest->merge([
                    "formula"=>$bag->roll_size_find,
                    "bookingBagUnits"=>"M", 
                    "bagL"=> $request->l,
                    "bagW"=> $request->w,
                    "bagG"=> $request->g,
                ]);
                $result = $this->calculatePossibleProduction($newRequest);
                $fromSize = (int)($result["result"]??0);
                $uptoSize = $fromSize+3;
                $roll->whereBetween("roll_details.size",[$fromSize,$uptoSize]);
                $transit->whereBetween("roll_transits.size",[$fromSize,$uptoSize]);
            }
            $rolls = collect($roll->get())->union($transit->get());
            $data["altGsm"] = collect($rolls->pluck("gsm")->unique())->sort()->values();
            return responseMsgs(true,"",$data);

        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function oldOrderOfClient(Request $request){
        try{
            // DB::enableQueryLog();
            $roll = $this->_M_OrderPunches
                    ->select(DB::raw("order_punch_details.bag_type_id,
                                    order_punch_details.bag_quality,
                                    order_punch_details.bag_gsm,
                                    order_punch_details.units,
                                    order_punch_details.total_units,
                                    order_punch_details.rate_per_unit,
                                    order_punch_details.bag_w,
                                    order_punch_details.bag_l,
                                    order_punch_details.bag_g,
                                    order_punch_details.bag_loop_color,
                                    order_punch_details.bag_color,
                                    order_punch_details.created_at,
                                    order_punch_details.grade_id,
                                    order_punch_details.rate_type_id,
                                    order_punch_details.fare_type_id,
                                    order_punch_details.stereo_type_id,
                                    bag_type_masters.bag_type,
                                    order_punch_details.bag_printing_color::text,
                                    grade_masters.grade,
                                    qualities.roll_quality,
                                    qualities.roll_ids
                                      ")
                    )
                    ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")
                    ->leftJoin(DB::raw("(
                        select order_id, 
                            string_agg(distinct(roll_quality_masters.quality),',') as roll_quality,
                            string_agg(distinct(rolls.id)::text,',') as roll_ids 
                        from order_roll_bag_types
                        join (
                            select id,quality_id
                            from roll_details
                            union all (
                                select id,quality_id
                                from roll_transits
                            )
                        ) as rolls on rolls.id = order_roll_bag_types.roll_id
                        join roll_quality_masters on roll_quality_masters.id = rolls.quality_id
                        group by order_id
                        ) as qualities"),"qualities.order_id","order_punch_details.id")
                    ->LeftJoin("grade_masters","grade_masters.id","order_punch_details.grade_id")
                    ->where("order_punch_details.client_detail_id",$request->clientId)
                    ->where("order_punch_details.lock_status",false)
                    ->orderBy("order_punch_details.created_at","DESC")
                    ->get()
                    ->map(function($val){
                        $val->stereo_type_id = $val->stereo_type_id ? 2:null;
                        $val->bag_gsm = is_Numeric($val->bag_gsm) ? json_encode([$val->bag_gsm]):$val->bag_gsm ;
                        return $val;
                    });

            // Remove duplicates based on the specified columns
            $roll = $roll->sortByDesc('created_at')->unique(function ($item) {
                return implode('|', [
                    $item->bag_type_id, $item->bag_quality, $item->bag_gsm, 
                    // $item->units, 
                    // $item->total_units, 
                    // $item->rate_per_unit, 
                    $item->bag_w, $item->bag_l, 
                    $item->bag_g,
                    //  $item->bag_loop_color, $item->bag_color, $item->bag_type,
                    // $item->grade_id,$item->rate_type_id,$item->fare_type_id,$item->stereo_type_id,
                    $item->roll_quality,
                ]);
            })->values();

                    // dd(DB::getQueryLog(),$roll);
            return responseMsgs(true,"old history",$roll);
            

        }catch(ExcelExcel $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function orderSuggestionClient(Request $request){
        try{
            if($request->bagQuality=="BOPP"){
                $request->merge(["bagGsm"=>array_sum(explode("/",$request->bagGsmJson))]);
            }
            if($request->bookingBagTypeId==3 && $request->totalUnits){
                $request->merge(["totalUnits"=> $request->totalUnits + ($request->totalUnits * 0.12)]);
            }
            $bag = $this->_M_BagType->find($request->bookingBagTypeId);
            $roll=$this->_M_RollDetail->select("roll_details.*",DB::raw("'stock' as stock, client_detail_masters.client_name,vendor_detail_masters.vendor_name,roll_quality_masters.quality"))
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("roll_quality_masters","roll_quality_masters.id","roll_details.quality_id")
                    ->where("roll_details.is_cut",false)
                    ->where("roll_details.is_printed",false)
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.is_roll_sell",false);
                    
            $transit = $this->_M_RollTransit->select("roll_transits.*",DB::raw("'transit' as stock, client_detail_masters.client_name,vendor_detail_masters.vendor_name,roll_quality_masters.quality"))
                        ->leftJoin("client_detail_masters","client_detail_masters.id","roll_transits.client_detail_id")
                        ->leftJoin("vendor_detail_masters","vendor_detail_masters.id","roll_transits.vender_id")
                        ->leftJoin("roll_quality_masters","roll_quality_masters.id","roll_transits.quality_id")
                        ->where("roll_transits.is_cut",false)
                        ->where("roll_transits.is_printed",false)
                        ->where("roll_transits.lock_status",false)
                        ->where("roll_transits.is_roll_sell",false);
                        
            if($request->bagQuality){
                $roll->where("roll_details.roll_type",$request->bagQuality);
                $transit->where("roll_transits.roll_type",$request->bagQuality);
            }
            if($request->bagGsm){
                $bagGsm = $request->bagGsm;
                if(!is_array($request->bagGsm))
                {
                    $bagGsm=[$request->bagGsm];
                }
                if($request->altBagGsm){
                    $bagGsm = array_merge($bagGsm,$request->altBagGsm);
                }
                $bagGsm = array_map(function($val){
                    return (int) $val;
                },$bagGsm);
                $roll->whereIn("roll_details.gsm",$bagGsm);
                $transit->whereIn("roll_transits.gsm",$bagGsm);
            }
            if($request->bookingBagColor){
                $bag_color = $request->bookingBagColor;
                if(!is_array($request->bookingBagColor))
                {
                    $bag_color=[$request->bookingBagColor];
                }
                if($request->altBagColor){
                    $bag_color = array_merge($bag_color,$request->altBagColor);
                }
                $roll->whereIn("roll_details.roll_color",$bag_color);
                $transit->whereIn("roll_transits.roll_color",$bag_color);
            }
            if($request->gradeId){
                $quality = $this->_M_RollQualityMaster->where("grade_id",$request->gradeId)->get()->pluck("id");
                $roll->whereIn("roll_details.quality_id",$quality);
                $transit->whereIn("roll_transits.quality_id",$quality);
            }
            if($bag){
                $newRequest = new Request();
                $newRequest->merge([
                    "formula"=>$bag->roll_size_find,
                    "bookingBagUnits"=>"M",                    
                    // "gsm" => $request->bagGsm,
                    "bagL"=> $request->l,
                    "bagW"=> $request->w,
                    "bagG"=> $request->g,
                ]);
                $result = $this->calculatePossibleProduction($newRequest);
                $fromSize = (int)($result["result"]??0);
                $uptoSize = $fromSize+3;
                $roll->whereBetween("roll_details.size",[$fromSize,$uptoSize]);
                $transit->whereBetween("roll_transits.size",[$fromSize,$uptoSize]);
            }

            $roll= $roll->get();
            $transit = $transit->get();
            if($request->bookingBagTypeId && $request->totalUnits && $request->bookingBagUnits){
                $bestFind = "";
                $bestFind2 = ""; 
                if($request->bookingBagUnits=="Kg"){
                    $bestFind = "RW";
                    $bestFind2 = "RW";
                }elseif($request->bookingBagUnits=="Piece"){
                    $bestFind = $bag->roll_find;                    
                    $bestFind2 = $bag->roll_find_as_weight;
                }
                $roll = $roll->map(function($val)use($request,$bestFind,$bestFind2){
                    $newRequest = new Request($val->toArray());
                    $newRequest->merge([
                        "formula"=>$bestFind,
                        "bookingBagUnits"=>$request->bookingBagUnits,
                        "length" => $val->length,
                        "netWeight" => $val->net_weight,
                        "size" => $val->size,
                        "gsm" => $val->gsm,
                        "bagL"=> $request->l,
                        "bagW"=> $request->w,
                        "bagG"=> $request->g,
                    ]);
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula"=>$bestFind2
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    $result1 = $this->calculatePossibleProduction($newRequest2);
                    $avg = round((($result["result"]??0)+($result1["result"]??0))/2);
                    $val->result = $avg; 
                    $val->unit =  $avg." ".$request->bookingBagUnits; 
                    return $val;
                })//->where("result","<=",$request->totalUnits)
                ->sortByDesc(function ($item) {
                    return [$item['result'], $item['size']];
                });

                $transit = $transit->map(function($val)use($request,$bestFind,$bestFind2){
                    $newRequest = new Request($val->toArray());
                    $newRequest->merge([
                        "formula"=>$bestFind,
                        "bookingBagUnits"=>$request->bookingBagUnits,
                        "length" => $val->length,
                        "netWeight" => $val->net_weight,
                        "size" => $val->size,
                        "gsm" => $val->gsm,
                        "bagL"=> $request->l,
                        "bagW"=> $request->w,
                        "bagG"=> $request->g,
                    ]);
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula"=>$bestFind2
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    $result1 = $this->calculatePossibleProduction($newRequest2);
                    $avg = round((($result["result"]??0)+($result1["result"]??0))/2);
                    $val->result = $avg; 
                    $val->unit =  $avg." ".$request->bookingBagUnits; 
                    return $val;
                })//->where("result","<=",$request->totalUnits)
                ->sortByDesc(function ($item) {
                    return [$item['result'], $item['size']];
                });
            }
                        
            $data["roll"]= collect($roll->values());
            $data["rollTransit"]= collect($transit->values());
            $data["size"] = [$fromSize,$uptoSize];
            // dd($request->all());
            return responseMsgs(true,"data Fetched",$data);
        }catch(ExcelExcel $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function orderPunchesSave(Request $request){
        try{
            $request->merge([
                "clientDetailId"=>$request->bookingForClientId,
                "estimateDeliveryDate"=>$request->bookingEstimatedDespatchDate,
                "bagTypeId"=>$request->bookingBagTypeId,
                "units"=>$request->bookingBagUnits,
                "bag_w"=>$request->w,
                "bag_l"=>$request->l,
                "bag_g"=>$request->g,
                "bag_loop_color"=>$request->looColor,
                "bag_color"=>$request->bookingBagColor,
                "bag_printing_color"=>$request->bookingPrintingColor,
            ]); 
            if($request->bagQuality!="BOPP"){
                $request->merge(["bagGsmJson"=>null]);
            }
            if($request->bagQuality=="BOPP"){
                $request->merge(["bagGsm"=>json_encode(array_sum(explode("/",$request->bagGsmJson)))]);
                $request->merge(["bagGsmJson"=>(explode("/",$request->bagGsmJson))]);
            }
            if($request->saveAsDraft){
                $request->merge(["is_draft"=>$request->saveAsDraft]);
            }

            DB::beginTransaction();
            if($request->id){
                $orderId = $request->id;
            }else{
                $orderId = $this->_M_OrderPunches->store($request);
            }
            $orderNew = $this->_M_OrderPunches->find($orderId);
            $type ="Pending";
            if($request->saveAsDraft){
                $type ="Draft";
            }
            $bookOrders = 0;
            if($request->roll){
                $type ="Booked";
                foreach($request->roll as $val){
                    $roll = $this->_M_RollDetail->find($val["id"]);
                    if(!$roll){
                        $roll = $this->_M_RollTransit->find($val["id"]);
                    }
                    if($roll->client_detail_id){
                        $orderRoll = $this->_M_OrderRollBagType->where("roll_id",$roll->id)->where("lock_status",false)->first();
                        if($orderRoll){
                            $order = $this->_M_OrderPunches->find($orderRoll->order_id);
                            $bag = $this->_M_BagType->find($order->bag_type_id);
                            $bestFind = "";
                            $bestFind2 ="";
                            if($order->units=="Kg"){
                                $bestFind = "RW";
                                $bestFind2 = "RW";
                            }elseif($order->units=="Piece"){
                                $bestFind = $bag->roll_find;
                                $bestFind2 = $bag->roll_find_as_weight;
                            }
                            $newRequest = new Request();
                            $newRequest->merge(
                                [
                                "bookingBagUnits" => $order->units,
                                "formula" => $bestFind,
                                "length" => $roll->length,
                                "netWeight"=>$roll->net_weight,
                                "size"=>$roll->size,
                                "gsm"=>$roll->gsm,

                                "bagL"=>$order->bag_l,
                                "bagW"=>$order->bag_w,
                                "bagG"=>$order->bag_g
                                ]
                            );
                            $newRequest2 = new Request($newRequest->all());
                            $newRequest2->merge([
                                "formula"=>$bestFind2
                            ]);
                            $result = $this->calculatePossibleProduction($newRequest);
                            $result2 = $this->calculatePossibleProduction($newRequest2);
                            $qty = ((($result["result"]??0)+($result2["result"]??0))/2);

                            $newRequest->merge([
                                "formula" => $bag->roll_find,
                            ]);
                            $newRequest2->merge([
                                "formula" => $bag->roll_find_as_weight,
                            ]);
                            $pieces = $this->calculatePossibleProduction($newRequest);
                            $pieces2 = $this->calculatePossibleProduction($newRequest2);
                            $totalPiece = ((($pieces["result"]??0)+($pieces2["result"]??0))/2); 
                            $totalLoopWeight = (($totalPiece*3.4)/1000);
                            if(in_array($bag->id,[2,4,5])){
                                $loopStock = $this->_M_LoopStock->where("loop_color",$order->bag_loop_color)->first();

                                $newLoopAccRequest = new Request(
                                    [
                                        "loop_stock_id"=>$loopStock->id,
                                        "roll_id"=>$roll->id,
                                        "order_id"=>$order->id,
                                        "description"=>"Roll Remove From Booking",
                                        "opening_balance"=>$loopStock->balance,
                                        "credit"=>0,
                                        "debit"=>$totalLoopWeight,
                                        "balance"=>$loopStock->balance +  $totalLoopWeight,
                                        "user_id"=>Auth()->user()->id
                                    ]
                                );
                                $this->_M_LoopAccount->store($newLoopAccRequest);

                                $loopStock->balance = $loopStock->balance + $totalLoopWeight;
                                $loopStock->update();
                            }
                            $order->booked_units = $order->booked_units - $qty;
                            $order->disbursed_units = 0;
                            $orderRoll->lock_status=true;
                            $order->update();
                            $orderRoll->update();
                        }                        
                    }
                    $roll->client_detail_id = $orderNew->client_detail_id;
                    $roll->estimate_delivery_date = $orderNew->estimate_delivery_date;
                    $roll->bag_type_id = $orderNew->bag_type_id;
                    $roll->bag_unit = $orderNew->units;
                    $roll->loop_color = $orderNew->bag_loop_color;
                    $roll->w = $orderNew->bag_w;
                    $roll->l = $orderNew->bag_l;
                    $roll->g = $orderNew->bag_g;
                    $roll->printing_color = $orderNew->bag_printing_color?json_decode($orderNew->bag_printing_color,true):null;
                    $roll->update();
                    $newRequest = new Request($roll->toArray());
                    $newRequest->merge(["order_id"=>$orderId,"roll_id"=>$roll->id]);
                    $this->_M_OrderRollBagType->store($newRequest);

                    $bag = $this->_M_BagType->find($orderNew->bag_type_id);
                    $formula = "";
                    $formula2 = "";
                    if($orderNew->units=="Kg"){
                        $formula = "RW";
                        $formula2 = "RW";
                    }elseif($orderNew->units=="Piece"){
                        $formula = $bag->roll_find;
                        $formula2 = $bag->roll_find_as_weight;
                    }
                    $newRequest = new Request();
                    $newRequest->merge(
                        [
                        "bookingBagUnits" => $orderNew->units,
                        "formula" => $formula,
                        "length" => $roll->length,
                        "netWeight"=>$roll->net_weight,
                        "size"=>$roll->size,
                        "gsm"=>$roll->gsm,

                        "bagL"=>$orderNew->bag_l,
                        "bagW"=>$orderNew->bag_w,
                        "bagG"=>$orderNew->bag_g
                        ]
                    );
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula" => $formula2,
                    ]); 
                    $result = $this->calculatePossibleProduction($newRequest);
                    $result2 = $this->calculatePossibleProduction($newRequest2);
                    $bookOrders += ((($result["result"]??0)+($result2["result"]??0))/2); 

                    $newRequest->merge([
                        "formula" => $bag->roll_find,
                    ]);
                    $newRequest2->merge([
                        "formula" => $bag->roll_find_as_weight,
                    ]);
                    $pieces = $this->calculatePossibleProduction($newRequest);
                    $pieces2 = $this->calculatePossibleProduction($newRequest2);
                    $totalPiece = ((($pieces["result"]??0)+($pieces2["result"]??0))/2); 
                    $totalLoopWeight = (($totalPiece*3.4)/1000);
                    if(in_array($bag->id,[2,4,5])){
                        $loopStock = $this->_M_LoopStock->where("loop_color",$orderNew->bag_loop_color)->first();

                        $newLoopAccRequest = new Request(
                            [
                                "loop_stock_id"=>$loopStock->id,
                                "roll_id"=>$roll->id,
                                "order_id"=>$orderNew->id,
                                "description"=>"Roll Add To Booking",
                                "opening_balance"=>$loopStock->balance,
                                "credit"=>$totalLoopWeight,
                                "debit"=>0,
                                "balance"=>$loopStock->balance -  $totalLoopWeight,
                                "user_id"=>Auth()->user()->id
                            ]
                        );
                        $this->_M_LoopAccount->store($newLoopAccRequest);
                        $loopStock->balance = $loopStock->balance - $totalLoopWeight;
                        $loopStock->update();
                    }
                } 
                $orderNew = $this->_M_OrderPunches->find($orderId);               
                $orderNew->booked_units = $orderNew->booked_units+$bookOrders;
            }
            $orderNew->update();
            DB::commit();
            return responseMsgs(true,"Order Place On $type","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function orderPunchesEdit(Request $request){
        $order = $this->_M_OrderPunches->find($request->orderId);
        if(!$order){
            flashToast("message","Data not found");
            return redirect()->back();
        }
        if($order->deceived_by){
            flashToast("message","Data Order is Deactivated");
            return redirect()->back();
        }
        if($order->is_delivered){
            flashToast("message","Order is Delivered. So that you can not edit");
            return redirect()->back();
        }
        if($request->post()){
            try{

                $request->merge([
                    "estimateDeliveryDate"=>$request->bookingEstimatedDespatchDate,
                    "bagTypeId"=>$request->bookingBagTypeId,
                    "units"=>$request->bookingBagUnits,
                    "bag_w"=>$request->w,
                    "bag_l"=>$request->l,
                    "bag_g"=>$request->g,
                    "bag_loop_color"=>$request->looColor,
                    "bag_color"=>$request->bookingBagColor,
                    "bag_printing_color"=>$request->bookingPrintingColor,
                ]); 
                if($request->bagQuality!="BOPP"){
                    $request->merge(["bagGsmJson"=>null]);
                }
                if($request->bagQuality=="BOPP"){
                    $request->merge(["bagGsm"=>[array_sum(explode("/",$request->bagGsmJson))]]);
                    $request->merge(["bagGsmJson"=>(explode("/",$request->bagGsmJson))]);
                }
                $request->merge(["id"=>$order->id]);
                if($request->is_daft_order==1){
                    $request->merge(["is_draft"=>false]);
                }
                DB::beginTransaction();
                $this->_M_OrderPunches->edit($request);
                DB::commit();
                flashToast("message","Order Updated");
                if($priv=request()->session()->pull("back")){
                    return redirect($priv["prevUrl"]);
                }
                return redirect()->route('order.unbook');
            }catch (Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Order update failed.']);
            }
            

        }
        $order->bag_gsm_json = $order->bag_gsm_json ? implode("/",json_decode($order->bag_gsm_json,true)) : $order->bag_gsm_json;
        $order->bag_printing_color = $order->bag_printing_color ? json_decode($order->bag_printing_color,true):[];
        $order->bag_color = $order->bag_color ? json_decode($order->bag_color,true):[];
        $order->bag_gsm = collect(json_decode($order->bag_gsm,true))->map(function($val){
            return (int) $val;
        })->toArray();
        $order->bookRoll = $order->getOrderRollBagTypes()->count()?true:false;
        $data["is_daft_order"] = $request->is_daft_order??false;
        $data["prevUrl"] = $request->priv_url??url()->previous();
        $data["prevUrlName"] = $request->priv_url_name??"Back";
        $data["order"] = $order;
        $data["clientList"] = $this->_M_ClientDetails->getClientListOrm()->orderBy("client_name","ASC")->get();
        $data["bagType"] = $this->_M_BagType->getBagListOrm()->orderBy("id")->get();
        $data["color"] = $this->_M_Color->getColorListOrm()->orderBy("id")->get();
        $data["rollColor"]=$this->_M_RollColor->getRollColorListOrm()->orderBy("id")->get();
        $data["grade"]=$this->_M_GradeMaster->getGradeListOrm()->orderBy("id")->get();
        $data["fare"]=$this->_M_FareDetail->getFareListOrm()->orderBy("id")->get();
        $data["stereo"]=$this->_M_StereoDetail->getStereoListOrm()->orderBy("id")->get();
        $data["rateType"] = $this->_M_RateTypeMaster->getRateTypeListOrm()->orderBy("id")->get();
        $data["loopColor"] = $this->_M_LoopStock->getLoopColorOrm()->where("balance",">",0)->orderBy("id")->get();
        $data["broker"] = $this->_M_OrderBroker->getBrokerOrm()->orderBy("broker_name","ASC")->get();
        $rollStockGsm = $this->_M_RollDetail->select('gsm')->distinct()->pluck('gsm');
        $rollTransitGsm = $this->_M_RollTransit->select('gsm')->where("gsm",">",2)->distinct()->pluck('gsm');
        $data["gsm"] = $rollStockGsm->union($rollTransitGsm)->unique()->sort()->values();
        request()->session()->put('back', ["prevUrl"=>$data['prevUrl'],"prevUrlName"=>$data["prevUrlName"]]);
        return view("Roll/orderEdit",$data);
    }

    public function bookedOrder_old1(Request $request){
        
        if($request->ajax())
        {
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",                                
                    )
                    ->join(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    (
                                        SELECT order_roll_bag_types.order_id, STRING_AGG(roll_details.roll_no,' , ') as roll_no , 
                                            STRING_AGG(bag_type_masters.bag_type,' , ') as bag_type, 
                                            STRING_AGG(roll_details.bag_unit,' , ') as bag_unit, 
                                            STRING_AGG(roll_details.printing_color,' , ') AS printing_color
                                        FROM order_roll_bag_types
                                        JOIN (
                                            SELECT 
                                                roll_details.id, roll_details.roll_no, roll_details.bag_type_id, roll_details.bag_unit,
                                                '(' || STRING_AGG(jsonb_element.value, ', ') || ')' AS printing_color
                                            FROM roll_details
                                            LEFT JOIN LATERAL jsonb_array_elements_text(roll_details.printing_color::jsonb) AS jsonb_element(value) ON TRUE
                                            GROUP BY roll_details.id
                                        ) as roll_details on roll_details.id = order_roll_bag_types.roll_id
                                        JOIN bag_type_masters on bag_type_masters.id = roll_details.bag_type_id
                                        GROUP BY order_roll_bag_types.order_id
                                    )
                                    UNION ALL(
                                        SELECT order_roll_bag_types.order_id, STRING_AGG(roll_transits.roll_no,' , ') as roll_no , 
                                            STRING_AGG(bag_type_masters.bag_type,' , ') as bag_type, 
                                            STRING_AGG(roll_transits.bag_unit,' , ') as bag_unit, 
                                            STRING_AGG(roll_transits.printing_color,' , ') AS printing_color
                                        FROM order_roll_bag_types
                                        JOIN (
                                            SELECT 
                                                roll_transits.id, roll_transits.roll_no, roll_transits.bag_type_id, roll_transits.bag_unit,
                                                '(' || STRING_AGG(jsonb_element.value, ', ') || ')' AS printing_color
                                            FROM roll_transits
                                            LEFT JOIN LATERAL jsonb_array_elements_text(roll_transits.printing_color::jsonb) AS jsonb_element(value) ON TRUE
                                            GROUP BY roll_transits.id
                                        ) as roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                        JOIN bag_type_masters on bag_type_masters.id = roll_transits.bag_type_id
                                        GROUP BY order_roll_bag_types.order_id
                                    )
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")                   
                    ->where("order_punch_details.lock_status",false)
                    ->orderBy("order_punch_details.created_at","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }
            // DB::enableQueryLog();
            // $data->get();
            // dd(DB::getQueryLog());            
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/bookedOrder");
    }

    public function bookedOrder(Request $request){
        if($request->ajax())
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",  
                                "bag_type_masters.bag_type" ,                       
                    )
                    ->join(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    SELECT order_id, STRING_AGG(roll_no,' , ') as roll_no 
                                    FROM(
                                        (
                                            SELECT order_roll_bag_types.order_id, STRING_AGG(roll_details.roll_no,' , ') as roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_details on roll_details.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                            GROUP BY order_roll_bag_types.order_id
                                        )
                                        UNION ALL(
                                            SELECT order_roll_bag_types.order_id, STRING_AGG(roll_transits.roll_no,' , ') as roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                            GROUP BY order_roll_bag_types.order_id
                                        )
                                    ) AS orders
                                    GROUP BY order_id 
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id") 
                    ->leftJoin("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")                   
                    ->where("order_punch_details.lock_status",false)
                    ->where("order_punch_details.is_delivered",false)
                    ->where("order_punch_details.booked_units",">",0)
                    ->where(function($where){
                        $where->where(DB::raw("order_punch_details.total_units"),"<=",DB::raw("order_punch_details.booked_units + order_punch_details.disbursed_units"));
                    })
                    ->orderBy("order_punch_details.estimate_delivery_date","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }   
                
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn("bag_printing_color",function($val){
                    return $val->bag_printing_color ? collect(json_decode($val->bag_printing_color,true))->implode(",") : "";
                })
                ->addColumn("bag_color",function($val){
                    return collect(json_decode($val->bag_color,true))->implode(", ") ;
                })
                ->addColumn("bag_gsm",function($val){
                    return collect(json_decode($val->bag_gsm,true))->implode(", ") ;
                })
                ->addColumn("total_units",function($val){
                    return round($val->total_units);
                })
                ->addColumn("booked_units",function($val){
                    return round($val->booked_units);
                })
                ->addColumn("bag_size",function($val){
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('action', function ($val) {  
                    $button="";
                    if(in_array(Auth()->user()->user_type_id,[1,2]) && $val->client_detail_id==1){
                        $button .='<a href = "'.route('order.punches.edit',['orderId' => $val->id,"priv_url"=>request()->path(),"priv_url_name"=>"Not Delivered"]).'" class="btn btn-sm btn-primary mx-2">E</a>';
                    }
                    return '<div style="display:flex">'.$button.'</div>';                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/bookedOrder");
    }

    public function bookedOrderDelivered(Request $request){
        if($request->ajax())
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",  
                                "bag_type_masters.bag_type" ,                       
                    )
                    ->join(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    SELECT order_id, STRING_AGG(roll_no,' , ') as roll_no 
                                    FROM(
                                        (
                                            SELECT order_roll_bag_types.order_id, STRING_AGG(roll_details.roll_no,' , ') as roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_details on roll_details.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                            GROUP BY order_roll_bag_types.order_id
                                        )
                                        UNION ALL(
                                            SELECT order_roll_bag_types.order_id, STRING_AGG(roll_transits.roll_no,' , ') as roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                            GROUP BY order_roll_bag_types.order_id
                                        )
                                    ) AS orders
                                    GROUP BY order_id 
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id") 
                    ->leftJoin("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")                   
                    ->where("order_punch_details.lock_status",false)
                    ->where("order_punch_details.is_delivered",true)
                    ->orderBy("order_punch_details.created_at","DESC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }         
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn("bag_color",function($val){
                    return $val->bag_color ? collect(json_decode($val->bag_color,true))->implode(",") : "";
                })
                ->addColumn("bag_color",function($val){
                    return collect(json_decode($val->bag_color,true))->implode(", ") ;
                })
                ->addColumn("bag_gsm",function($val){
                    return collect(json_decode($val->bag_gsm,true))->implode(", ") ;
                })
                ->addColumn("total_units",function($val){
                    return round($val->total_units);
                })
                ->addColumn("booked_units",function($val){
                    return round($val->booked_units);
                })
                ->addColumn("bag_size",function($val){
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : Carbon::parse($val->updated_at)->format("d-m-Y") ;                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/bookedOrderDelivered");
    }

    public function orderStatus(Request $request){

        if($request->ajax())
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*","bg.total_delivered","bg.total_not_delivered","bg.total_bag",
                                "client_detail_masters.client_name",  
                                "bag_type_masters.bag_type" ,                       
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    SELECT order_id, STRING_AGG(roll_no,' , ') as roll_no, 
                                        SUM(total_booked_roll) AS total_booked_roll, 
                                        SUM(total_printed_roll) AS total_printed_roll, 
                                        SUM(total_cut_roll) AS total_cut_roll

                                    FROM(
                                        (
                                            SELECT order_roll_bag_types.order_id, STRING_AGG(roll_details.roll_no,' , ') as roll_no ,
                                                count( roll_details.id ) as total_booked_roll, 
                                                count( CASE WHEN roll_details.is_printed OR roll_details.printing_color IS NULL THEN roll_details.id END ) as total_printed_roll,
                                                count( CASE WHEN roll_details.is_cut THEN roll_details.id END ) as total_cut_roll
                                            FROM order_roll_bag_types
                                            JOIN roll_details on roll_details.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                            GROUP BY order_roll_bag_types.order_id
                                        )
                                        UNION ALL(
                                            SELECT order_roll_bag_types.order_id, STRING_AGG(roll_transits.roll_no,' , ') as roll_no ,
                                                count( roll_transits.id ) as total_booked_roll, 
                                                count( CASE WHEN roll_transits.is_printed OR roll_transits.printing_color IS NULL THEN roll_transits.id END ) as total_printed_roll,
                                                count( CASE WHEN roll_transits.is_cut THEN roll_transits.id END ) as total_cut_roll
                                            FROM order_roll_bag_types
                                            JOIN roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                            GROUP BY order_roll_bag_types.order_id
                                        )
                                    ) AS orders
                                    GROUP BY order_id 
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )
                    ->leftJoin(DB::raw("(select order_id , count(id) as total_bag,count(case when packing_status=4 then id end) as total_delivered,
                                            count(case when packing_status!=4 then id end) as total_not_delivered
                                        from bag_packings
                                        where bag_packings.lock_status = false
                                        group by order_id) AS bg"),"bg.order_id","order_punch_details.id")                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id") 
                    ->leftJoin("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")                   
                    ->where("order_punch_details.lock_status",false)
                    // ->where("order_punch_details.order_no","10")
                    ->orderBy("order_punch_details.order_date","DESC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.order_date::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.order_date::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.order_date::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            } 
            $data = $data->get()->map(function($val) {
                $bookAndDisbursed = $val->booked_units + $val->disbursed_units;
                $totalUnits = $val->total_units;
            
                // Step 1: Roll Booking Status
                $isRollBook = $val->total_booked_roll > 0;
                $isRollNotBook = !$isRollBook;
            
                // Initialize status variables
                $isFullBook = $isPartBook = false;
                $isFullBookFullPrint = $isFullBookPartPrint = false;
                $isFullBookFullPrintFullCut = $isFullBookFullPrintPartCut = false;
                $isFullBookPartPrintFullCut = $isFullBookPartPrintPartCut = false;
                $isPartBookFullPrint = $isPartBookPartPrint = false;
                $isPartBookFullPrintFullCut = $isPartBookFullPrintPartCut = false;
                $isPartBookPartPrintFullCut = $isPartBookPartPrintPartCut = false;
                $isFullDelivered = false;
                $isPartDelivered = false;
            
                // Step 2: Check Full or Partial Booking (Only if Roll Booking is done)
                if ($isRollBook) {
                    $isFullBook = $totalUnits <= $bookAndDisbursed;
                    $isPartBook = !$isFullBook && $val->booked_units > 0;
                }
            
                // Step 3: Check Printing Status (Only if Booking is done)
                if ($isFullBook && $val->total_printed_roll) {
                    $isFullBookFullPrint = $val->total_printed_roll == $val->total_booked_roll;
                    $isFullBookPartPrint = !$isFullBookFullPrint;
                }
            
                if ($isPartBook && $val->total_printed_roll) {
                    $isPartBookFullPrint = $val->total_printed_roll == $val->total_booked_roll;
                    $isPartBookPartPrint = !$isPartBookFullPrint;
                }
            
                // Step 4: Check Cutting Status (Only if **Printing is fully completed** and **Roll is actually Cut**)
                if ($isFullBookFullPrint && $val->total_cut_roll > 0) { 
                    $isFullBookFullPrintFullCut = $val->total_cut_roll == $val->total_booked_roll;
                    $isFullBookFullPrintPartCut = !$isFullBookFullPrintFullCut;
                }
            
                if ($isFullBookPartPrint && $val->total_cut_roll > 0) {
                    $isFullBookPartPrintFullCut = $val->total_cut_roll == $val->total_booked_roll ;
                    $isFullBookPartPrintPartCut = !$isFullBookPartPrintFullCut;
                }
            
                if ($isPartBookFullPrint && $val->total_cut_roll > 0) {
                    $isPartBookFullPrintFullCut = $val->total_cut_roll == $val->total_booked_roll;
                    $isPartBookFullPrintPartCut = !$isPartBookFullPrintFullCut;
                }
            
                if ($isPartBookPartPrint && $val->total_cut_roll > 0) {
                    $isPartBookPartPrintFullCut = $val->total_cut_roll == $val->total_booked_roll;
                    $isPartBookPartPrintPartCut = !$isPartBookPartPrintFullCut;
                }

                // Step 5: Check Delivery Status (Only if **Cutting is completed**)
                if ($val->total_delivered > 0) {
                    if ($isFullBookFullPrintFullCut || $isFullBookPartPrintFullCut) {
                        $isFullDelivered = $val->total_delivered == $val->total_bag;
                        $isPartDelivered = !$isFullDelivered;
                    }

                    if ($isPartBookFullPrintFullCut || $isPartBookPartPrintFullCut) {
                        $isPartDelivered = $val->total_not_delivered == $val->total_bag;
                        $isFullDelivered = !$isPartDelivered;
                    } 
                    if($isFullBookFullPrintPartCut || $isFullBookPartPrintPartCut){
                        $isPartDelivered = $val->total_not_delivered == $val->total_bag;
                        $isFullDelivered = !$isPartDelivered;
                    }
                    if($isPartBookFullPrintPartCut || $isPartBookPartPrintPartCut){
                        $isPartDelivered = $val->total_not_delivered == $val->total_bag;
                        $isFullDelivered = !$isPartDelivered;
                    }
                }
            
                // Assign final statuses
                $val->isRollBook = $isRollBook;
                $val->isRollNotBook = $isRollNotBook;
                $val->isFullBook = $isFullBook;
                $val->isPartBook = $isPartBook;
                $val->isFullBookFullPrint = $isFullBookFullPrint;
                $val->isFullBookPartPrint = $isFullBookPartPrint;
                $val->isPartBookFullPrint = $isPartBookFullPrint;
                $val->isPartBookPartPrint = $isPartBookPartPrint;
                $val->isFullBookFullPrintFullCut = $isFullBookFullPrintFullCut;
                $val->isFullBookFullPrintPartCut = $isFullBookFullPrintPartCut;
                $val->isFullBookPartPrintFullCut = $isFullBookPartPrintFullCut;
                $val->isFullBookPartPrintPartCut = $isFullBookPartPrintPartCut;
                $val->isPartBookFullPrintFullCut = $isPartBookFullPrintFullCut;
                $val->isPartBookFullPrintPartCut = $isPartBookFullPrintPartCut;
                $val->isPartBookPartPrintFullCut = $isPartBookPartPrintFullCut;
                $val->isPartBookPartPrintPartCut = $isPartBookPartPrintPartCut;
                $val->isFullDelivered = $isFullDelivered;
                $val->isPartDelivered = $isPartDelivered;
            
                return $val;
            });
            
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn("bag_color",function($val){
                    return $val->bag_color ? collect(json_decode($val->bag_color,true))->implode(",") : "";
                })
                ->addColumn("bag_color",function($val){
                    return collect(json_decode($val->bag_color,true))->implode(", ") ;
                })
                ->addColumn("bag_gsm",function($val){
                    return collect(json_decode($val->bag_gsm,true))->implode(", ") ;
                })
                ->addColumn("total_units",function($val){
                    return round($val->total_units);
                })
                ->addColumn("booked_units",function($val){
                    return round($val->booked_units);
                })
                ->addColumn("bag_size",function($val){
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : Carbon::parse($val->updated_at)->format("d-m-Y") ;                    
                })
                ->addColumn('status', function ($order) {
                    return view('Roll.partials.order_status', compact('order'))->render();
                })
                ->addColumn('flowchart', function ($order) {
                    return view('Roll.partials.order_flowchart', compact('order'))->render();
                })
                ->rawColumns(['status', 'flowchart'])
                ->make(true);
            return $list;

        }

        return view("Roll/orderStatus");
    }

    public function unBookedOrder_old1(Request $request){
        
        if($request->ajax())
        {
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","pending_order_bag_types.*",
                                "client_detail_masters.client_name",
                                
                    )
                    ->join(
                        DB::raw("(
                            SELECT pending_order_bag_types.order_id, 
                                STRING_AGG( DISTINCT(bag_type_masters.bag_type),' , ') as bag_type, 
		                        STRING_AGG( DISTINCT(pending_order_bag_types.bag_unit),' , ') as bag_unit, 
                                '(' || STRING_AGG(jsonb_element.value, ', ') || ')' AS printing_color
                            FROM pending_order_bag_types
                            JOIN bag_type_masters on bag_type_masters.id = pending_order_bag_types.bag_type_id
                            LEFT JOIN LATERAL jsonb_array_elements_text(pending_order_bag_types.printing_color::jsonb) AS jsonb_element(value) ON TRUE
                            GROUP BY pending_order_bag_types.order_id
                        ) AS pending_order_bag_types")
                        ,"pending_order_bag_types.order_id","order_punch_details.id")
                    ->leftJoin("order_roll_bag_types", "order_roll_bag_types.order_id","order_punch_details.id")                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")                    
                    ->where("order_punch_details.lock_status",false)
                    ->whereNull("order_roll_bag_types.order_id")
                    ->orderBy("order_punch_details.created_at","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn("roll_no",function($val){
                    return "";
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/unBookedOrder");
    }

    public function unBookedOrder(Request $request){
        
        if($request->ajax())
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",  
                                "bag_type_masters.bag_type" , 
                                "grade_masters.grade" ,                       
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    SELECT order_id, STRING_AGG(roll_no,' , ') as roll_no 
                                    FROM(
                                        (
                                            SELECT order_roll_bag_types.order_id, roll_details.roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_details on roll_details.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                        )
                                        UNION ALL(
                                            SELECT order_roll_bag_types.order_id, roll_transits.roll_no
                                            FROM order_roll_bag_types
                                            JOIN roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                        )
                                    )orders
                                    GROUP BY order_id 
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id") 
                    ->leftJoin("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")    
                    ->leftJoin("grade_masters","grade_masters.id","order_punch_details.grade_id")                   
                    ->where("order_punch_details.lock_status",false)
                    ->where("order_punch_details.is_delivered",false)
                    ->where("order_punch_details.is_draft",false)
                    ->where(function($where){
                        $where->where(DB::raw("order_punch_details.total_units"),">",DB::raw("order_punch_details.booked_units + order_punch_details.disbursed_units"));
                    })
                    ->orderBy("order_punch_details.created_at","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }     
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn("bag_size",function($val){
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn("bag_printing_color",function($val){
                    return collect(json_decode($val->bag_printing_color,true))->implode(", ") ;
                })
                ->addColumn("bag_color",function($val){
                    return collect(json_decode($val->bag_color,true))->implode(", ") ;
                })
                ->addColumn("bag_gsm",function($val){
                    return collect(json_decode($val->bag_gsm,true))->implode(", ") ;
                })
                ->addColumn("alt_bag_color",function($val){
                    return collect(json_decode($val->alt_bag_color,true))->implode(", ") ;
                })
                ->addColumn("alt_bag_gsm",function($val){
                    return collect(json_decode($val->alt_bag_gsm,true))->implode(", ") ;
                })
                
                ->addColumn("total_units",function($val){
                    return round($val->total_units) ;
                })
                ->addColumn("booked_units",function($val){
                    return round($val->booked_units) ;
                })
                ->addColumn("balance_units",function($val){
                    return round($val->total_units -( $val->booked_units + $val->disbursed_units));
                })
                ->addColumn("balance_units_kg",function($val){
                    return $val->units=='Kg' ? round($val->total_units -( $val->booked_units + $val->disbursed_units)) : "N/A";
                })
                ->addColumn("balance_units_pcs",function($val){
                    return $val->units!='Kg' ? round($val->total_units -( $val->booked_units + $val->disbursed_units)) : "N/A";
                })
                ->addColumn("balance_units_in_kg",function($val){
                    $balance = round($val->total_units -( $val->booked_units + $val->disbursed_units));
                    $bag = $val->getBagType();
                    $formula = $bag->weight_of_bag_per_piece??"";
                    $size = $bag->roll_size_find;
                    $newRequest = new Request();
                    $newRequest->merge([
                        "formula"=>$formula,
                        "bagL" =>$val->bag_l,
                        "size" =>" ( ".$size." ) ",
                        "bagL"=>$val->bag_l,                        
                        "bagW"=>$val->bag_w,
                        "bagG"=>$val->bag_g,
                    ]);                    
                    $kg=[];
                    $gsmArr = json_decode($val->bag_gsm,true);
                    if($gsmArr && is_array($gsmArr) && $val->units=="Piece"){
                        
                        foreach($gsmArr as $gsm){
                            $newRequest->merge([
                                "gsm"=>$gsm
                            ]);
                            $result = $this->calculatePossibleProduction($newRequest);
                            $kg[] = ["key"=>$gsm,"val"=> roundFigure(($result["result"] * $balance)/1000)];
                        }
                    }
                    return $kg;
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn("order_date",function($val){
                    return $val->order_date ? Carbon::parse($val->order_date)->format("d-m-Y"):"";
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('action', function ($val) {     
                    $button ='<i class="btn btn-sm btn-success mx-2" onClick="openBookingModel('.$val->id.')">S</i>'; 
                    $button .='<a href = "'.route('order.punches.edit',['orderId' => $val->id,"priv_url"=>request()->path(),"priv_url_name"=>"Order Punches"]).'" class="btn btn-sm btn-primary mx-2">E</a>';      
                    $button .= '<i class="btn btn-sm btn-warning mx-2" onClick="disbursedOrder('.$val->id.')" >D</i>';
                    $button .= '<i class="btn btn-sm btn-danger mx-2" onClick="deactivate('.$val->id.')" >X</i>';         
                    return '<div style="display:flex">'.$button.'</div>';                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/unBookedOrder");
    }

    public function draftOrder(Request $request){
        if($request->ajax()){
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",  
                                "bag_type_masters.bag_type" , 
                                "grade_masters.grade" ,                       
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    SELECT order_id, STRING_AGG(roll_no,' , ') as roll_no 
                                    FROM(
                                        (
                                            SELECT order_roll_bag_types.order_id, roll_details.roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_details on roll_details.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                        )
                                        UNION ALL(
                                            SELECT order_roll_bag_types.order_id, roll_transits.roll_no
                                            FROM order_roll_bag_types
                                            JOIN roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                        )
                                    )orders
                                    GROUP BY order_id 
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id") 
                    ->leftJoin("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")    
                    ->leftJoin("grade_masters","grade_masters.id","order_punch_details.grade_id")                   
                    ->where("order_punch_details.lock_status",false)
                    ->where("order_punch_details.is_delivered",false)
                    ->where("order_punch_details.is_draft",true)                    
                    ->orderBy("order_punch_details.created_at","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }     
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn("bag_size",function($val){
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn("bag_printing_color",function($val){
                    return collect(json_decode($val->bag_printing_color,true))->implode(", ") ;
                })
                ->addColumn("bag_color",function($val){
                    return collect(json_decode($val->bag_color,true))->implode(", ") ;
                })
                ->addColumn("bag_gsm",function($val){
                    return collect(json_decode($val->bag_gsm,true))->implode(", ") ;
                })
                ->addColumn("alt_bag_color",function($val){
                    return collect(json_decode($val->alt_bag_color,true))->implode(", ") ;
                })
                ->addColumn("alt_bag_gsm",function($val){
                    return collect(json_decode($val->alt_bag_gsm,true))->implode(", ") ;
                })
                
                ->addColumn("total_units",function($val){
                    return round($val->total_units) ;
                })
                ->addColumn("booked_units",function($val){
                    return round($val->booked_units) ;
                })
                ->addColumn("balance_units",function($val){
                    return round($val->total_units -( $val->booked_units + $val->disbursed_units));
                })
                ->addColumn("balance_units_kg",function($val){
                    return $val->units=='Kg' ? round($val->total_units -( $val->booked_units + $val->disbursed_units)) : "N/A";
                })
                ->addColumn("balance_units_pcs",function($val){
                    return $val->units!='Kg' ? round($val->total_units -( $val->booked_units + $val->disbursed_units)) : "N/A";
                })
                ->addColumn("balance_units_in_kg",function($val){
                    $balance = round($val->total_units -( $val->booked_units + $val->disbursed_units));
                    $bag = $val->getBagType();
                    $formula = $bag->weight_of_bag_per_piece??"";
                    $size = $bag->roll_size_find??"";
                    $newRequest = new Request();
                    $newRequest->merge([
                        "formula"=>$formula,
                        "bagL" =>$val->bag_l,
                        "size" =>" ( ".$size." ) ",
                        "bagL"=>$val->bag_l,                        
                        "bagW"=>$val->bag_w,
                        "bagG"=>$val->bag_g,
                    ]);                    
                    $kg=[];
                    $gsmArr = json_decode($val->bag_gsm,true);
                    if($gsmArr && is_array($gsmArr) && $val->units=="Piece"){
                        
                        foreach($gsmArr as $gsm){
                            $newRequest->merge([
                                "gsm"=>$gsm
                            ]);
                            $result = $this->calculatePossibleProduction($newRequest);
                            $kg[] = ["key"=>$gsm,"val"=> roundFigure(($result["result"] * $balance)/1000)];
                        }
                    }
                    return $kg;
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn("order_date",function($val){
                    return $val->order_date ? Carbon::parse($val->order_date)->format("d-m-Y"):"";
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('action', function ($val) {     
                    $button ='<i class="btn btn-sm btn-success mx-2" onClick="openBookingModel('.$val->id.')" title="search roll">S</i>'; 
                    $button .='<a href = "'.route('order.punches.edit',['orderId' => $val->id,"priv_url"=>request()->path(),"priv_url_name"=>"Draft Order","is_daft_order"=>true]).'" class="btn btn-sm btn-primary mx-2" title="Edit Order">E</a>';      
                    // $button .= '<i class="btn btn-sm btn-warning mx-2" onClick="disbursedOrder('.$val->id.')" title="Disburse Order">D</i>';
                    $button .= '<i class="btn btn-sm btn-danger mx-2" onClick="deactivate('.$val->id.')" title="Delete Order">X</i>';         
                    return '<div style="display:flex">'.$button.'</div>';                    
                })
                ->make(true);
            return $list;

        }
        }
        return view("Roll/draftOrder");
    }

    public function rebookOrder(Request $request){
        try{
            $order = $this->_M_OrderPunches->find($request->id);
            $bag = $order->getBagType();
            $client = $order->getClient();
            $grade = $order->getGrade();
            $rateType = $order->getRateType();
            $fare = $order->getFare();
            $stereo = $order->getStereo();
            $order->bag_type = $bag->bag_type??"";
            $order->client_name = $client->client_name??"";
            $order->grade = $grade->grade??"";
            $order->rate_type = $rateType->rate_type??"";
            $order->fare_type = $fare->fare_type??"";
            $order->stereo_type = $stereo->stereo_type??"";
            
            $request->merge([
                "bagQuality"=>$order->bag_quality,
                "gradeId"=>$order->grade_id,
                "ratePerUnit"=>$order->rate_per_unit,
                "rateTypeId"=>$order->rate_type_id,
                "fareTypeId"=>$order->fare_type_id,
                "stereoTypeId"=>$order->stereo_type_id,
                "bookingBagColor"=>collect(json_decode($order->bag_color,true))->toArray(),//$order->bag_color,
                "bagGsm"=>collect(json_decode($order->bag_gsm,true))->toArray(),//$order->bag_gsm,
                "bagGsmJson"=>collect(json_decode($order->bag_gsm_json,true))->implode("/"),
                "bookingBagTypeId"=>$order->bag_type_id,
                "totalUnits"=> $order->total_units -  $order->disbursed_units, //$order->booked_units -
                "bookingBagUnits"=>$order->units,
                "altBagGsm"=> collect(json_decode($order->alt_bag_gsm,true))->toArray(),
                "altBagColor"=>collect(json_decode($order->alt_bag_color,true))->toArray(),

                "l"=>$order->bag_l,
                "w"=>$order->bag_w,
                "g"=>$order->bag_g,
                
            ]);
            $result = $this->orderSuggestionClient($request)->original;
            $order->bag_gsm_json = collect(json_decode($order->bag_gsm_json,true))->implode("/");
            $data["order"] =$order;
            $data["roll"] =[];
            $data["rollTransit"] =[];
            if($result["status"]){
                $data["roll"] = $result["data"]["roll"];
                $data["rollTransit"] = $result["data"]["rollTransit"];
            }
            $message="Data Fetched";
            if(sizeof($data["roll"])<=0 && sizeof($data["rollTransit"])<=0){
                $message=" Roll Not Found For ".$request->totalUnits." ".$request->bookingBagUnits;
            }
            return responseMsgs(true,$message,$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollTestByOrder(Request $request){
        try{
            $rollId = $request->id;
            $orderId = $request->bookingForClientId;
            $roll = $this->_M_RollTransit->find($rollId);
            if(!$roll){
                $roll= $this->_M_RollDetail->find($rollId);
            }
            $order = $this->_M_OrderPunches->find($orderId);
            $newRequest = new Request();
            $newRequest->merge([
                "bagQuality"=>$order->bag_quality,
                "gradeId"=>$order->grade_id,
                "ratePerUnit"=>$order->rate_per_unit,
                "rateTypeId"=>$order->rate_type_id,
                "fareTypeId"=>$order->fare_type_id,
                "stereoTypeId"=>$order->stereo_type_id,
                "bookingBagColor"=>collect(json_decode($order->bag_color,true))->toArray(),//$order->bag_color,
                "bagGsm"=>collect(json_decode($order->bag_gsm,true))->toArray(),//$order->bag_gsm,
                "bagGsmJson"=>collect(json_decode($order->bag_gsm_json,true))->implode("/"),
                "bookingBagTypeId"=>$order->bag_type_id,
                "totalUnits"=> $order->total_units - $order->booked_units - $order->disbursed_units,
                "bookingBagUnits"=>$order->units,
                "altBagGsm"=> collect(json_decode($order->alt_bag_gsm,true))->toArray(),
                "altBagColor"=>collect(json_decode($order->alt_bag_color,true))->toArray(),

                "l"=>$order->bag_l,
                "w"=>$order->bag_w,
                "g"=>$order->bag_g,
                
            ]); 
            $result = $this->orderSuggestionClient($newRequest)->original;
            if(!$result["status"]){
                throw new Exception($result["message"]);                
            }
            $test = false;
            $production =0;
            $units="";
            if($roll && $roll->getTable()=="roll_details"){
                $test = collect($result["data"]["roll"])->where("id",$rollId)->count()>0 ? true : false;
                $testedRoll = collect($result["data"]["roll"])->where("id",$rollId)->first();
                $production = $testedRoll->result??0;
                $units=$testedRoll->unit??"";
            }
            if($roll && $roll->getTable()=="roll_transits"){
                $test = collect($result["data"]["rollTransit"])->where("id",$rollId)->count()>0 ? true : false;
                $testedRoll = collect($result["data"]["rollTransit"])->where("id",$rollId)->first();
                $production = $testedRoll->result??0;
                $units=$testedRoll->unit??"";
            }
            $message="Roll is tested and suitable for this order";
            if(!$test){
                $message="Roll is tested and not suitable for this order";
            }
            $data["test"]=$test;
            $data["production"]=$production;
            $data["units"]=$units;
            $data["balanceUnit"]= round($order->total_units -( $order->booked_units + $order->disbursed_units)). " ".$order->units;
            $data["balance"]= round($order->total_units -( $order->booked_units + $order->disbursed_units));
            return responseMsgs(true,$message,$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function bookRollToOrder(Request $request){
        try{
            $rule = [
                "id"=>"required",
                "bookingForClientId"=>"required",
            ];
            $newRequest = new Request([
                "id"=>$request->bookingForClientId,
                "roll"=>[
                    ["id"=>$request->id],
                ]
                ]);
                return $this->orderPunchesSave($newRequest);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function backInTransit(Request $request){
        try{
            $rule = [
                "id"=>"required",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }
            DB::beginTransaction();
            $response = $this->removeBookedRoll($request);
            if(!$response->original["status"]){
                return $response;
            }
            $roll = $this->_M_RollDetail->find($request->id);
            $transitDtl =$roll->replicate();
            $transitDtl->setTable($this->_M_RollTransit->getTable());
            $transitDtl->id = $roll->id;
            $transitDtl->roll_receiving_at = null;
            $transitDtl->save();
            $roll->forceDelete();
            DB::commit();
            return responseMsgs(true,"Roll Remove From Stock","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
    public function removeBookedRoll(Request $request){
        try{
            $rule = [
                "id"=>"required",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }
            $roll = $this->_M_RollDetail->find($request->id);
            if(!$roll){
                $roll = $this->_M_RollTransit->find($request->id);
            }
            if($roll->is_printed){
                throw new Exception("Roll is Printed");
            }
            if($roll->is_cut){
                throw new Exception("Roll is cut");
            }
            if($roll->is_roll_sell){
                throw new Exception("Roll is Sell");
            }
            DB::beginTransaction();
            if($roll->client_detail_id){
                $orderRoll = $this->_M_OrderRollBagType->where("roll_id",$roll->id)->where("lock_status",false)->first();
                if($orderRoll){
                    $order = $this->_M_OrderPunches->find($orderRoll->order_id);
                    $bag = $this->_M_BagType->find($order->bag_type_id);
                    $bestFind = "";
                    $bestFind2 = "";
                    if($order->units=="Kg"){
                        $bestFind = "RW";
                        $bestFind2 = "RW";
                    }elseif($order->units=="Piece"){
                        $bestFind = $bag->roll_find;
                        $bestFind2 = $bag->roll_find_as_weight;
                    }
                    $newRequest = new Request();
                    $newRequest->merge(
                        [
                        "bookingBagUnits" => $order->units,
                        "formula" => $bestFind,
                        "length" => $roll->length,
                        "netWeight"=>$roll->net_weight,
                        "size"=>$roll->size,
                        "gsm"=>$roll->gsm,

                        "bagL"=>$order->bag_l,
                        "bagW"=>$order->bag_w,
                        "bagG"=>$order->bag_g
                        ]
                    );
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula" => $bestFind2,
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    $result2 = $this->calculatePossibleProduction($newRequest2);
                    $order->booked_units = $order->booked_units - ((($result["result"]??0)+($result2["result"]??0))/2);
                    $order->disbursed_units = 0;
                    $orderRoll->lock_status=true;
                    $order->update();
                    $orderRoll->update();

                    $newRequest->merge([
                        "formula" => $bag->roll_find,
                    ]);
                    $newRequest2->merge([
                        "formula" => $bag->roll_find_as_weight,
                    ]);
                    $pieces = $this->calculatePossibleProduction($newRequest);
                    $pieces2 = $this->calculatePossibleProduction($newRequest2);
                    $totalPiece = ((($pieces["result"]??0)+($pieces2["result"]??0))/2); 
                    $totalLoopWeight = (($totalPiece*3.4)/1000);
                    if(in_array($bag->id,[2,4])){
                        $loopStock = $this->_M_LoopStock->where("loop_color",$order->bag_loop_color)->first();

                        $newLoopAccRequest = new Request(
                            [
                                "loop_stock_id"=>$loopStock->id,
                                "roll_id"=>$roll->id,
                                "order_id"=>$order->id,
                                "description"=>"Roll Remove From Booking",
                                "opening_balance"=>$loopStock->balance,
                                "credit"=>0,
                                "debit"=>$totalLoopWeight,
                                "balance"=>$loopStock->balance +  $totalLoopWeight,
                                "user_id"=>Auth()->user()->id
                            ]
                        );
                        $this->_M_LoopAccount->store($newLoopAccRequest);

                        $loopStock->balance = $loopStock->balance + $totalLoopWeight;
                        $loopStock->update();
                    }
                }                        
            }
            $roll->resizeRollFromClient($roll->id);
            DB::commit();
            return responseMsgs(true,"Roll Remove From Booking","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function disbursedOrder(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                "id"=>"required|exists:".$this->_M_OrderPunches->getTable().",id",
            ]);
            if($validate->fails()){
                return validationError($validate);
            }
            $order = $this->_M_OrderPunches->find($request->id);
            $order->disbursed_units = $order->total_units - $order->booked_units;
            $order->disbursed_by = Auth()->user()->id;
            DB::beginTransaction();
            $order->update();
            DB::commit();
            return responseMsgs(true,"Order Disbursed","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivateOrder(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                "id"=>"required|exists:".$this->_M_OrderPunches->getTable().",id",
            ]);
            if($validate->fails()){
                return validationError($validate);
            }
            $order = $this->_M_OrderPunches->find($request->id);
            
            $rollTransit = $order->getRollTransit()->get(); 
            $roll = $order->getRollDetail()->get();             
            $order->lock_status = true;
            $order->deceived_by = Auth()->user()->id;
            $test1=$rollTransit->filter(function ($item) {
                return $item->is_printed || $item->is_cut;
            })->count();
            $test2=$roll->filter(function ($item) {
                return $item->is_printed || $item->is_cut;
            })->count();
            if($test1 || $test2){
                throw new Exception("Some Roll are printed or cut");
            }

            DB::beginTransaction();
            foreach($rollTransit as $val){
                $val->resizeRollFromClient($val->id);
            }
            foreach($roll as $val){
                $val->resizeRollFromClient($val->id);
            }
            $order->update();
            DB::commit();
            return responseMsgs(true,"Order Is Deactivated","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function disburseRegister(Request $request){
        if($request->ajax())
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",  
                                "bag_type_masters.bag_type" ,  
                                "users.name"                     
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    SELECT order_id, STRING_AGG(roll_no,' , ') as roll_no 
                                    FROM(
                                        (
                                            SELECT order_roll_bag_types.order_id, roll_details.roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_details on roll_details.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                        )
                                        UNION ALL(
                                            SELECT order_roll_bag_types.order_id, roll_transits.roll_no
                                            FROM order_roll_bag_types
                                            JOIN roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                        )
                                    )orders
                                    GROUP BY order_id 
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id") 
                    ->leftJoin("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")  
                    ->leftJoin("users","users.id","order_punch_details.disbursed_by")                 
                    ->where("order_punch_details.lock_status",false)
                    ->where("order_punch_details.disbursed_units",">",0)
                    ->orderBy("order_punch_details.created_at","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }         
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn("bag_size",function($val){
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ? (" x ".(float)$val->bag_g) : "") ;
                })
                ->addColumn("bag_printing_color",function($val){
                    return collect(json_decode($val->bag_printing_color,true))->implode(", ") ;
                })
                ->addColumn("bag_color",function($val){
                    return collect(json_decode($val->bag_color,true))->implode(", ") ;
                })
                ->addColumn("bag_gsm",function($val){
                    return collect(json_decode($val->bag_gsm,true))->implode(", ") ;
                })
                
                ->addColumn("total_units",function($val){
                    return round($val->total_units);
                })
                ->addColumn("booked_units",function($val){
                    return round($val->booked_units) ;
                })
                ->addColumn("disbursed_units",function($val){
                    return round($val->disbursed_units) ;
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/disburseRegister");
    }

    public function rollUpdate(Request $request){
        try{
            $roll = $this->_M_RollDetail->find($request->editRollId);
            if(!$roll){
                $roll = $this->_M_RollTransit->find($request->editRollId);
            }
            if($roll->is_printed){
                throw new Exception("Roll Is Printed");
            }
            if($roll->is_cut){
                throw new Exception("Roll Is Cut");
            }
            if($roll->is_roll_sell){
                throw new Exception("Roll Is Sell");
            }
            $roll->quality_id = $request->qualityId;
            $roll->roll_type = $request->rollType;
            $roll->hardness = $request->hardness;
            $roll->gsm = $request->gsm;
            $roll->gsm_json = $request->gsmJson;
            $roll->roll_color = $request->rollColor;
            $roll->length = $request->length;
            $roll->size = $request->size;
            $roll->net_weight = $request->netWeight;
            $roll->gross_weight = $request->grossWeight;
            $roll->printing_color = $request->printingColor;
            DB::beginTransaction();
            $roll->update();
            DB::commit();
            return responseMsgs(true,"Roll Update","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function swapRollNo(Request $request){
        try{
            $rules = [
                "roll" => "required|array",
                "roll.firstRoll.*" => "required|exists:" . $this->_M_RollDetail->getTable() . ",roll_no,is_printed,false,is_cut,false,is_roll_sell,false",
                "roll.secondRoll.*" => [
                    "required",
                    "exists:" . $this->_M_RollDetail->getTable() . ",roll_no,is_printed,false,is_cut,false,is_roll_sell,false",
                    function ($attr, $value, $fail) use ($request) {
                        $key = explode(".",$attr)[2];
                        $firstRollNo = $request->input('roll.firstRoll.'.$key);
                        $secondRollNo = $value;
                        
                        if($firstRollNo==$secondRollNo){
                            $fail("firstRoll and secondRoll must be deferent");
                            return;
                        }
                        // Fetch roll details for all rolls in firstRoll and secondRoll
                        $firstRollDetails = $this->_M_RollDetail
                            ->where('roll_no', $firstRollNo)
                            ->first();
                        $secondRollDetails = $this->_M_RollDetail
                            ->where('roll_no', $secondRollNo)
                            ->first();
            
                        $columnsToMatch = [
                            'vendor_id', 'gsm', 'gsm_json', 
                            'roll_color', //'length', 
                            'size', 
                            "quality_id",
                            //'net_weight', 
                            'hardness', 
                            'roll_type'
                        ];
                        if($firstRollDetails && $secondRollDetails){
                            foreach ($columnsToMatch as $column) {
                                if ($firstRollDetails->$column !== $secondRollDetails->$column) {
                                    $fail("firstRoll and secondRoll must have the same value for the column: $column.");
                                    return;
                                }
                            }
                        }
                    },
                ],
            ];
            
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return validationError($validate);
            }dd($request->all());
            DB::beginTransaction();
            foreach($request->roll["firstRoll"] as $index=> $val){
                $rollNo1 = $val;
                $rollNo2 = $request->roll["secondRoll"][$index];
                $roll1 = $this->_M_RollDetail->where("roll_no",$rollNo1)->first();
                $roll2 = $this->_M_RollDetail->where("roll_no",$rollNo2)->first();
                if($roll1->is_printed){
                    throw new Exception("Roll No. ".$roll1->roll_no." is printed");
                }
                if($roll1->is_cut){
                    throw new Exception("Roll No. ".$roll1->roll_no." is cut");
                }
                if($roll2->is_printed){
                    throw new Exception("Roll No. ".$roll2->roll_no." is printed");
                }
                if($roll2->is_cut){
                    throw new Exception("Roll No. ".$roll2->roll_no." is cut");
                }
                $roll1->roll_no = null;
                $roll2->roll_no = null;
                $roll1->update();
                $roll2->update();
                $roll1->roll_no = $rollNo2;
                $roll2->roll_no = $rollNo1;
                $roll1->update();
                $roll2->update();
            }
            DB::commit();
            return responseMsgs(true,"Roll No. Swap","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function swapSelectedRoll(Request $request){
        try{
            $rules = [
                "roll" => "required|array",
                "roll.firstRoll.*" => "required|exists:" . $this->_M_RollDetail->getTable() . ",id,is_printed,false,is_cut,false,is_roll_sell,false",
                "roll.secondRoll.*" => [
                    "required",
                    "exists:" . $this->_M_RollDetail->getTable() . ",id,is_printed,false,is_cut,false,is_roll_sell,false",
                    function ($attr, $value, $fail) use ($request) {
                        $key = explode(".",$attr)[2];
                        $firstRollId = $request->input('roll.firstRoll.'.$key);
                        $secondRollId = $value;
                        
                        if($firstRollId==$secondRollId){
                            $fail("firstRoll and secondRoll must be deferent");
                            return;
                        }
                        // Fetch roll details for all rolls in firstRoll and secondRoll
                        $firstRollDetails = $this->_M_RollDetail
                            ->where('id', $firstRollId)
                            ->first();
                        $secondRollDetails = $this->_M_RollDetail
                            ->where('id', $secondRollId)
                            ->first();
            
                        $columnsToMatch = [
                            'vendor_id', 'gsm', 'gsm_json', 
                            'roll_color', //'length', 
                            // 'size', 
                            "quality_id",
                            //'net_weight', 
                            'hardness', 
                            'roll_type'
                        ];
                        if($firstRollDetails && $secondRollDetails){
                            foreach ($columnsToMatch as $column) {
                                if ($firstRollDetails->$column !== $secondRollDetails->$column) {
                                    $fail("firstRoll and secondRoll must have the same value for the column: $column.");
                                    return;
                                }
                            }
                        }
                    },
                ],
            ];
            
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return validationError($validate);
            }DB::enableQueryLog();
            DB::beginTransaction();
            foreach($request->roll["firstRoll"] as $index=> $val){
                $rollId1 = $val;
                $rollId2 = $request->roll["secondRoll"][$index];
                $roll1 = $this->_M_RollDetail->where("id",$rollId1)->first();
                $roll2 = $this->_M_RollDetail->where("id",$rollId2)->first();

                $testSize1 = false;
                $testSize2 = false;
                $order_filed=["client_detail_id","estimate_delivery_date","printing_color","loop_color","bag_type_id","bag_unit","w","l","g"];
                $avg1 = $avgNew1 = 0;
                $avg2 = $avgNew2 = 0;
                $bag1 = $this->_M_BagType->find($roll1->bag_type_id);
                $bag2 = $this->_M_BagType->find($roll2->bag_type_id);
                if($bag1){
                    $newRequest = new Request();
                    $newRequest->merge([
                        "formula"=>$bag1->roll_size_find,
                        "bookingBagUnits"=>"M",                    
                        // "gsm" => $request->bagGsm,
                        "bagL"=> $roll1->l,
                        "bagW"=> $roll1->w,
                        "bagG"=> $roll1->g,
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    $fromSize1 = (int)($result["result"]??0);
                    $uptoSize1 = $fromSize1+3;
                    if(is_between($roll2->size,$fromSize1,$uptoSize1)){
                        $testSize1=true;
                    }

                    // possible production 

                    $bestFind = "";
                    $bestFind2 = ""; 
                    if($roll1->bag_unit=="Kg"){
                        $bestFind = "RW";
                        $bestFind2 = "RW";
                    }elseif($roll1->bag_unit=="Piece"){
                        $bestFind = $bag1->roll_find;                    
                        $bestFind2 = $bag1->roll_find_as_weight;
                    }

                    $newRequestOld = new Request($roll1->toArray());
                    $newRequestOld->merge([
                        "formula"=>$bestFind,
                        "bookingBagUnits"=>$roll1->bag_unit,
                        "length" => $roll1->length,
                        "netWeight" => $roll1->net_weight,
                        "size" => $roll1->size,
                        "gsm" => $roll1->gsm,
                        "bagL"=> $roll1->l,
                        "bagW"=> $roll1->w,
                        "bagG"=> $roll1->g,
                    ]);
                    $newRequestOld2 = new Request($newRequestOld->all());
                    $newRequestOld2->merge([
                        "formula"=>$bestFind2
                    ]);
                    $result = $this->calculatePossibleProduction($newRequestOld);
                    $result1 = $this->calculatePossibleProduction($newRequestOld2);
                    $avg1 = round((($result["result"]??0)+($result1["result"]??0))/2);

                    if($bag2){
                        $newRequestNew = new Request($roll2->toArray());
                        $newRequestNew->merge([
                            "formula"=>$bestFind,
                            "bookingBagUnits"=>$roll2->bag_unit,
                            "length" => $roll2->length,
                            "netWeight" => $roll2->net_weight,
                            "size" => $roll2->size,
                            "gsm" => $roll2->gsm,
                            "bagL"=> $roll2->l,
                            "bagW"=> $roll2->w,
                            "bagG"=> $roll2->g,
                        ]);
                        $newRequestNew2 = new Request($newRequestNew->all());
                        $newRequestNew2->merge([
                            "formula"=>$bestFind2
                        ]);
                        $result = $this->calculatePossibleProduction($newRequestNew);
                        $result1 = $this->calculatePossibleProduction($newRequestNew2);
                        $avgNew1 = round((($result["result"]??0)+($result1["result"]??0))/2);
                    }
                }
                if($bag2){
                    $newRequest = new Request();
                    $newRequest->merge([
                        "formula"=>$bag2->roll_size_find,
                        "bookingBagUnits"=>"M",                    
                        // "gsm" => $request->bagGsm,
                        "bagL"=> $roll2->l,
                        "bagW"=> $roll2->w,
                        "bagG"=> $roll2->g,
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    $fromSize2 = (int)($result["result"]??0);
                    $uptoSize2 = $fromSize2+3;
                    if(is_between($roll1->size,$fromSize2,$uptoSize2)){
                        $testSize2=true;
                    }

                    // possible production 

                    $bestFind = "";
                    $bestFind2 = ""; 
                    if($roll2->bag_unit=="Kg"){
                        $bestFind = "RW";
                        $bestFind2 = "RW";
                    }elseif($roll2->bag_unit=="Piece"){
                        $bestFind = $bag1->roll_find;                    
                        $bestFind2 = $bag1->roll_find_as_weight;
                    }

                    $newRequestOld = new Request($roll2->toArray());
                    $newRequestOld->merge([
                        "formula"=>$bestFind,
                        "bookingBagUnits"=>$roll2->bag_unit,
                        "length" => $roll2->length,
                        "netWeight" => $roll2->net_weight,
                        "size" => $roll2->size,
                        "gsm" => $roll2->gsm,
                        "bagL"=> $roll2->l,
                        "bagW"=> $roll2->w,
                        "bagG"=> $roll2->g,
                    ]);
                    $newRequestOld2 = new Request($newRequestOld->all());
                    $newRequestOld2->merge([
                        "formula"=>$bestFind2
                    ]);
                    $result = $this->calculatePossibleProduction($newRequestOld);
                    $result1 = $this->calculatePossibleProduction($newRequestOld2);
                    $avg2 = round((($result["result"]??0)+($result1["result"]??0))/2);

                    if($bag1){
                        $newRequestNew = new Request($roll1->toArray());
                        $newRequestNew->merge([
                            "formula"=>$bestFind,
                            "bookingBagUnits"=>$roll1->bag_unit,
                            "length" => $roll1->length,
                            "netWeight" => $roll1->net_weight,
                            "size" => $roll1->size,
                            "gsm" => $roll1->gsm,
                            "bagL"=> $roll1->l,
                            "bagW"=> $roll1->w,
                            "bagG"=> $roll1->g,
                        ]);
                        $newRequestNew2 = new Request($newRequestNew->all());
                        $newRequestNew2->merge([
                            "formula"=>$bestFind2
                        ]);
                        $result = $this->calculatePossibleProduction($newRequestNew);
                        $result1 = $this->calculatePossibleProduction($newRequestNew2);
                        $avgNew2 = round((($result["result"]??0)+($result1["result"]??0))/2);
                    }
                }
                
                $orderKey1=[];
                $orderKey2=[];
                foreach($order_filed as $key){
                    $orderKey1[$key]=$roll1[$key];
                    $orderKey2[$key]=$roll2[$key];
                }
                $orderRollBag1 = $this->_M_OrderRollBagType->where("roll_id",$roll1->id)->where("lock_status",false)->orderBy("id","DESC")->first();
                $orderRollBag2 = $this->_M_OrderRollBagType->where("roll_id",$roll2->id)->where("lock_status",false)->orderBy("id","DESC")->first();
                if($testSize1){
                    foreach($orderKey1 as $key=>$orderVal){
                        $roll2[$key] = $orderVal;
                    }
                    foreach($orderKey2 as $key=>$orderVal){
                        $roll1[$key] = $orderVal;
                    }

                    if($orderRollBag1){
                        $order1 = $this->_M_OrderPunches->find($orderRollBag1->order_id);
                        $order1->booked_units = $order1->booked_units - $avg1 + $avgNew1;
                        
                        $total_units = $order1->total_units;
                        $newDisbursed_units = $order1->total_units - $order1->booked_units;
                        if($newDisbursed_units<=0 || !$order1->disbursed_units){
                            $order1->disbursed_units = 0 ;
                        }
                        elseif($order1->disbursed_units && $newDisbursed_units>0 && $newDisbursed_units >= round(($total_units)/100*95)){
                            $order1->disbursed_units = $newDisbursed_units ;
                        }
                        elseif($order1->disbursed_units && $newDisbursed_units>0 && $newDisbursed_units < round(($total_units)/100*95)){
                            $order1->disbursed_units = 0;
                        }
                        $orderRollBag1->roll_id= $roll2->id;
                        
                        $orderRollBag1->update();
                        $order1->update();
                    }
                    
                }
                else{
                    // remover from booking;  
                    if($orderRollBag1){
                        $order1 = $this->_M_OrderPunches->find($orderRollBag1->order_id);
                        $order1->booked_units = $order1->booked_units - $avg1 ;
                        $order1->disbursed_units = 0;
                        $orderRollBag1->lock_status= true;
                        
                        $orderRollBag1->update();
                        $order1->update();
                    }                  
                }

                if($testSize2){
                    foreach($orderKey2 as $key=>$orderVal){
                        $roll1[$key] = $orderVal;
                    }
                    foreach($orderKey1 as $key=>$orderVal){
                        $roll2[$key] = $orderVal;
                    }
                    if($orderRollBag2){
                        $order2 = $this->_M_OrderPunches->find($orderRollBag2->order_id);
                        $order2->booked_units = $order2->booked_units - $avg2 + $avgNew2;

                        $total_units = $order2->total_units;
                        $newDisbursed_units = $order2->total_units - $order2->booked_units;
                        if($newDisbursed_units<=0 || !$order2->disbursed_units){
                            $order2->disbursed_units = 0 ;
                        }
                        elseif($order2->disbursed_units && $newDisbursed_units>0 && $newDisbursed_units >= round(($total_units)/100*95)){
                            $order2->disbursed_units = $newDisbursed_units ;
                        }
                        elseif($order2->disbursed_units && $newDisbursed_units>0 && $newDisbursed_units < round(($total_units)/100*95)){
                            $order2->disbursed_units = 0;
                        }

                        $orderRollBag2->roll_id= $roll1->id;
                        
                        $orderRollBag2->update();
                        $order2->update();
                    } 
                }
                else{
                    if($orderRollBag2){
                        $order2 = $this->_M_OrderPunches->find($orderRollBag2->order_id);
                        $order2->booked_units = $order2->booked_units - $avg2 ;
                        $order2->disbursed_units = 0;
                        $orderRollBag2->lock_status= true;
                        
                        $orderRollBag2->update();
                        $order2->update();
                    }
                }
                
                $rollNo1 = $roll1->roll_no;
                $rollNo2 = $roll2->roll_no;
                // dd($testSize1,$testSize2,$bag1 , $bag2);
                if((!($testSize1 || $testSize2)) && ($bag1 || $bag2)){
                    throw new MyException("Roll are not suitable for swapping");
                }
                if($roll1->is_printed){
                    throw new MyException("Roll No. ".$roll1->roll_no." is printed");
                }
                if($roll1->is_cut){
                    throw new MyException("Roll No. ".$roll1->roll_no." is cut");
                }
                if($roll2->is_printed){
                    throw new MyException("Roll No. ".$roll2->roll_no." is printed");
                }
                if($roll2->is_cut){
                    throw new MyException("Roll No. ".$roll2->roll_no." is cut");
                }
                // $roll1->roll_no = null;
                // $roll2->roll_no = null;
                // $roll1->update();
                // $roll2->update();
                // $roll1->roll_no = $rollNo2;
                // $roll2->roll_no = $rollNo1;
                $roll1->update();
                $roll2->update();
            }
            DB::commit();
            return responseMsgs(true,"Roll No. Swap","");
        }catch(MyException $e){
            return responseMsgs(false,$e->getMessage(),"");
        }catch(Exception $e){dd($e);
            return responseMsgs(false,"server error!!!","");
        }
    }
    

}
