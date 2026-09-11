<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Models\CityStateMap;
use App\Models\FareDetail;
use App\Models\GradeMaster;
use App\Models\LoopStock;
use App\Models\LoopUsageAccount;
use App\Models\OrderBroker;
use App\Models\RateTypeMaster;
use App\Models\RollQualityGradeMap;
use App\Models\RollQualityMaster;
use App\Models\RollShortageLimit;
use App\Models\StateMaster;
use App\Models\StereoDetail;
use App\Models\UserTypeMaster;
use App\Models\VendorDetailMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class MasterController extends Controller
{
    //
    protected $_M_FareDetail;
    protected $_M_StereoDetail;
    protected $_M_GradeMaster;
    protected $_M_RollQualityMaster;
    protected $_M_RollQualityGradeMap;
    protected $_M_VendorDetail;
    protected $_M_RateType;
    protected $_M_UserTypeMaster;
    protected $_M_LoopStock;
    protected $_M_LoopUsageAccount;
    protected $_M_broker;
    protected $_M_Sate;
    protected $_M_CityStateMap;
    protected $_M_RollShortageLimit;

    function __construct()
    {
        $this->_M_FareDetail = new FareDetail();
        $this->_M_StereoDetail = new StereoDetail();
        $this->_M_GradeMaster = new GradeMaster();
        $this->_M_RollQualityMaster = new RollQualityMaster();
        $this->_M_RollQualityGradeMap = new RollQualityGradeMap();
        $this->_M_VendorDetail = new VendorDetailMaster();
        $this->_M_RateType = new RateTypeMaster();
        $this->_M_UserTypeMaster = new UserTypeMaster();
        $this->_M_LoopStock = new LoopStock();
        $this->_M_LoopUsageAccount = new LoopUsageAccount();
        $this->_M_broker = new OrderBroker();
        $this->_M_Sate = new StateMaster();
        $this->_M_CityStateMap = new CityStateMap();
        $this->_M_RollShortageLimit = new RollShortageLimit();
    }


    public function fareList(Request $request){
        if($request->ajax()){
            $data = $this->_M_FareDetail->where("lock_status",false)->orderBy("id","ASC");
            if ($request->has('export')) {
                $columns = json_decode($request->export_columns, true);
        
                $headings = json_decode($request->export_headings,true);
                if(!$headings){
                    $headings = collect($columns)->map(function ($col) {
                        return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                    })->toArray();
                }
                $data=$data->get();
                if ($request->export === 'excel') {
                    return Excel::download(new DataExport($data, $headings,$columns), 'Broker-list.xlsx');
                }
            } 
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                          EOD;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/fare_list");
    }

    public function addFare(Request $request){
        try{
            $message = "New Fare Add";
            if($request->id){
                $this->_M_FareDetail->edit($request);
                $message = "Fare Update";
            }else{
                $this->_M_FareDetail->store($request);
            }
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivateFare($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_FareDetail->edit($request);
            return responseMsgs(true,"Fare Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    public function fareDtl($id){
        try{
            $data = $this->_M_FareDetail->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    /**
     * Stereo
     */
    public function stereoList(Request $request){
        if($request->ajax()){
            $data = $this->_M_StereoDetail->where("lock_status",false)->orderBy("id","ASC");
            if ($request->has('export')) {
                $columns = json_decode($request->export_columns, true);
        
                $headings = json_decode($request->export_headings,true);
                if(!$headings){
                    $headings = collect($columns)->map(function ($col) {
                        return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                    })->toArray();
                }
                $data=$data->get();
                if ($request->export === 'excel') {
                    return Excel::download(new DataExport($data, $headings,$columns), 'Stereo-list.xlsx');
                }
            } 
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                          EOD;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/stereo_list");
    }

    public function addStereo(Request $request){
        try{
            $message = "New Stereo Add";
            if($request->id){
                $this->_M_StereoDetail->edit($request);
                $message = "Stereo Update";
            }else{
                $this->_M_StereoDetail->store($request);
            }
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function stereoDtl($id){
        try{
            $data = $this->_M_StereoDetail->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivateStereo($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_StereoDetail->edit($request);
            return responseMsgs(true,"Stereo Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    /**
     * Grade
     */
    public function gradeList(Request $request){
        if($request->ajax()){
            $data = $this->_M_GradeMaster->where("lock_status",false)->orderBy("id","ASC");
            if ($request->has('export')) {
                $columns = json_decode($request->export_columns, true);
        
                $headings = json_decode($request->export_headings,true);
                if(!$headings){
                    $headings = collect($columns)->map(function ($col) {
                        return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                    })->toArray();
                }
                $data=$data->get();
                if ($request->export === 'excel') {
                    return Excel::download(new DataExport($data, $headings,$columns), 'Grade-list.xlsx');
                }
            } 
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                          EOD;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/grade_list");
    }

    public function addGrade(Request $request){
        try{
            $message = "New Grade Add";
            if($request->id){
                $this->_M_GradeMaster->edit($request);
                $message = "Grade Update";
            }else{
                $this->_M_GradeMaster->store($request);
            }
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function gradeDtl($id){
        try{
            $data = $this->_M_GradeMaster->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivateGrade($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_GradeMaster->edit($request);
            return responseMsgs(true,"Grade Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    /**
     * Roll Quality
     */
    public function rollQualityList(Request $request){
        if($request->ajax()){
            $data = $this->_M_VendorDetail->where("lock_status",false)->orderBy("id","ASC");
            if ($request->has('export')) {
                $columns = json_decode($request->export_columns, true);
        
                $headings = json_decode($request->export_headings,true);
                if(!$headings){
                    $headings = collect($columns)->map(function ($col) {
                        return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                    })->toArray();
                }
                $data=$data->get()->map(function($val){
                    $val->roll_quality = ($val->rollQualityList()->get())->pluck('quality')->implode(",","quality");
                    return $val;
                });
                if ($request->export === 'excel') {
                    return Excel::download(new DataExport($data, $headings,$columns), 'Roll-Quality-list.xlsx');
                }
            } 
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("roll_quality",function($val){
                    return ($val->rollQualityList()->get())->pluck('quality')->implode(",","quality");
                })
                ->addColumn('action', function ($val) {
                    return '<i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('.$val->id.')" ></i>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/roll_quality_list");
    }

    public function addRollQuality(Request $request){
        try{
            $rule = [
                "id"=>"required|exists:".$this->_M_VendorDetail->getTable().",id",
                "quality"=>"required|array",
                "quality.*.quality"=>"required"
            ];
            $Validator = Validator::make($request->all(),$rule);
            if($Validator->fails()){
                return validationError($Validator);
            }
            $request->merge(["vendorId"=>$request->id]);
            $message = "New Grade Add"; 
            $this->_M_RollQualityMaster->where("vendor_id",$request->vendorId)->update(["lock_status"=>true]);
            foreach($request->quality as $val){
                $newRequest = new Request($request->all());
                $newRequest->merge($val);
                $qualityId = $this->_M_RollQualityMaster->store($newRequest);
            }           
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollQualityDtl($id){
        try{
            $data = $this->_M_VendorDetail->find($id);
            $quality = $data->rollQualityList()->get();
            $data->roll_quality = $quality;
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function gradeListMap(Request $request){
        try{
            $data = $this->_M_GradeMaster->where("lock_status",false)->orderBy("grade","ASC")->get();         
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollVenderListMap($venderId){
        try{
            $data = $this->_M_RollQualityMaster->where("vendor_id",$venderId)->where("lock_status",false)->orderBy("quality","ASC")->get();         
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    /**
     * rate type
     */

    public function rateTypeList(Request $request){
        if($request->ajax()){
            $data = $this->_M_RateType->where("lock_status",false)->orderBy("id","ASC");
            if ($request->has('export')) {
                $columns = json_decode($request->export_columns, true);
        
                $headings = json_decode($request->export_headings,true);
                if(!$headings){
                    $headings = collect($columns)->map(function ($col) {
                        return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                    })->toArray();
                }
                $data=$data->get();
                if ($request->export === 'excel') {
                    return Excel::download(new DataExport($data, $headings,$columns), 'Rate-Type-list.xlsx');
                }
            } 
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                          EOD;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/rate_type_list");
    }

    public function addRateType(Request $request){
        try{
           
            $message = "New Rate Type"; 
            if($request->id){
                $message = "Rate Type Update"; 
                $this->_M_RateType->edit($request);
            }else{
                $this->_M_RateType->store($request);
            }       
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rateTypeDtl($id){
        try{
            $data = $this->_M_RateType->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    
    public function deactivateRateType($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_RateType->edit($request);
            return responseMsgs(true,"Rate Type Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    /**
     * Grade
     */
    public function userTypeList(Request $request){
        if($request->ajax()){
            $data = $this->_M_UserTypeMaster->where("lock_status",false)->orderBy("id","ASC");
            if ($request->has('export')) {
                $columns = json_decode($request->export_columns, true);
        
                $headings = json_decode($request->export_headings,true);
                if(!$headings){
                    $headings = collect($columns)->map(function ($col) {
                        return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                    })->toArray();
                }
                $data=$data->get();
                if ($request->export === 'excel') {
                    return Excel::download(new DataExport($data, $headings,$columns), 'User Type-list.xlsx');
                }
            } 
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                          EOD;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/user_type_list");
    }

    public function addUserType(Request $request){
        try{
            $message = "New User Type Add";
            if($request->id){
                $this->_M_UserTypeMaster->edit($request);
                $message = "User Type Update";
            }else{
                $this->_M_UserTypeMaster->store($request);
            }
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function userTypeDtl($id){
        try{
            $data = $this->_M_UserTypeMaster->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function loopStockList(Request $request){
       if($request->ajax()){
            $data = $this->_M_LoopStock->where("lock_status",false)->orderBy("id","ASC");
            if ($request->has('export')) {
                $columns = json_decode($request->export_columns, true);
        
                $headings = json_decode($request->export_headings,true);
                if(!$headings){
                    $headings = collect($columns)->map(function ($col) {
                        return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                    })->toArray();
                }
                $data=$data->get();
                if ($request->export === 'excel') {
                    return Excel::download(new DataExport($data, $headings,$columns), 'Loop-Stock-list.xlsx');
                }
            } 
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                          EOD;
                })
                ->rawColumns(['action'])
                ->make(true);
       }
       return view("Master/loop_stock_list");
    }

    public function loopStockAddEdit(Request $request){
        try{
            DB::beginTransaction();
            if($request->id){
                $this->_M_LoopStock->edit($request);
                $message = "User Type Update";
            }else{
                $this->_M_LoopStock->store($request);
            }
            DB::commit();
            return responseMsgs(true,"Loop Stock Updated","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function loopStockDtl($id){
        try{
            $data = $this->_M_LoopStock->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivateLoopStock($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_LoopStock->edit($request);
            return responseMsgs(true,"Loop Stock Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    public function loopStockTestBooking(Request $request){
        try{
            $stock = $this->_M_LoopStock->where("loop_color",$request->loopColor)->first();
            if(!$stock){
                throw new Exception("Invalid Color");
            }
            $message = "";
            $class = "";
            if($stock->balance==0){
                $message= $stock->loop_color." loop is not available in stock";
                $class = "error";
            }elseif($stock->balance < $stock->min_limit){
                $message= $stock->loop_color." loop is very short";
                $class = "warning";
            }elseif($stock->balance < ($stock->min_limit+100)){
                $message= $stock->loop_color." loop is nearly sorted.";
                $class = "info";
            }
            return responseMsgs(true,$message,$class);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    /**
     * Broker
     */

    public function brokerList(Request $request){
        if($request->ajax()){
            $data = $this->_M_broker->orderBy("id","ASC");
            if ($request->has('export')) {
                $columns = json_decode($request->export_columns, true);
        
                $headings = json_decode($request->export_headings,true);
                if(!$headings){
                    $headings = collect($columns)->map(function ($col) {
                        return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                    })->toArray();
                }
                $data=$data->get();
                if ($request->export === 'excel') {
                    return Excel::download(new DataExport($data, $headings,$columns), 'Agent-list.xlsx');
                }
            } 
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    $button = '<i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('.$val->id.')" ></i>';
                    $btn2 ='<i class="bi bi-lock-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('."'Are you sure you want to lock this item?'".', function() { deactivate('.$val->id.'); })" ></i>';
                    if($val->lock_status){
                        $btn2 ='<i class="bi bi-unlock-fill btn btn-sm" style ="color:rgb(37, 229, 37)" onclick="showConfirmDialog('."'Are you sure you want to unlock this item?'".', function() { activate('.$val->id.'); })" ></i>';
                    }
                    return $button.$btn2;
                })
                ->rawColumns(['action'])
                ->make(true);
       }
       return view("Master/broker_list");
    }

    public function brokerAddEdit(Request $request){
        try{
            DB::beginTransaction();
            $message = "New Broker Add";
            if($request->id){
                $this->_M_broker->edit($request);
                $message = "Broker Update";
            }else{
                $this->_M_broker->store($request);
            }
            DB::commit();
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function brokerDtl($id){
        try{
            $data = $this->_M_broker->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function activeDeactivateBroker($id,Request $request){
        try{
            $message="Broker Unlock";
            if($request->lock_status){
                $message="Broker Locked";
            }
            $request->merge(["id",$id]);
            $this->_M_broker->edit($request);
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    /**
     * State
     */

    public function stateList(Request $request){
        if($request->ajax()){
            $data = $this->_M_Sate->orderBy("state_name","ASC");
            if ($request->has('export')) {
                $columns = json_decode($request->export_columns, true);
        
                $headings = json_decode($request->export_headings,true);
                if(!$headings){
                    $headings = collect($columns)->map(function ($col) {
                        return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                    })->toArray();
                }
                $data=$data->get();
                if ($request->export === 'excel') {
                    return Excel::download(new DataExport($data, $headings,$columns), 'State-list.xlsx');
                }
            } 
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    $button = '<i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('.$val->id.')" ></i>';
                    $btn2 ='<i class="bi bi-lock-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('."'Are you sure you want to lock this item?'".', function() { deactivate('.$val->id.'); })" ></i>';
                    if($val->lock_status){
                        $btn2 ='<i class="bi bi-unlock-fill btn btn-sm" style ="color:rgb(37, 229, 37)" onclick="showConfirmDialog('."'Are you sure you want to unlock this item?'".', function() { activate('.$val->id.'); })" ></i>';
                    }
                    return $button.$btn2;
                })
                ->rawColumns(['action'])
                ->make(true);
       }
       return view("Master/state_list");
    }

    public function stateAddEdit(Request $request){
        try{
            DB::beginTransaction();
            $message = "New State Add";
            if($request->id){
                $this->_M_Sate->edit($request);
                $message = "State Update";
            }else{
                $this->_M_Sate->store($request);
            }
            DB::commit();
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function stateDtl($id){
        try{
            $data = $this->_M_Sate->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function activeDeactivateState($id,Request $request){
        try{
            $message="State Unlock";
            if($request->lock_status){
                $message="State Locked";
            }
            $request->merge(["id",$id]);
            $this->_M_Sate->edit($request);
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    public function importStateAndCity(Request $request){
        $row="";
        $index="";
        if($request->post()){            
            ini_set('max_execution_time', 600);
            $file = $request->file('csvFile');
            $rows = Excel::toArray([], $file);
            $headings=[];
            DB::beginTransaction();
            try{
                foreach ($rows[0] as $index => $row) {
                    // Skip the header row
                    if ($index == 0) {
                        $headings = $row;
                        continue;
                    }
                    $rowData = array_combine($headings, $row);
                    $testState = $this->_M_Sate->where("state_name",$rowData["stateName"])->first();
                    if(!$testState){
                        $stateRequest = new Request($rowData);
                        $stateId = $this->_M_Sate->store($stateRequest);
                    }else{
                        $stateId = $testState->id;
                    }
                    $testCity = $this->_M_CityStateMap->where("state_id",$stateId)->where("city_name",$rowData["cityName"])->first();
                    if(!$testCity){
                        $citeRequest = new Request($rowData);
                        $citeRequest->merge(["state_id"=>$stateId]);
                        $this->_M_CityStateMap->store($citeRequest);
                    }
                }
                DB::commit();
                flashToast("message","data Import");
                return redirect()->to('/master/state/list');
            }catch(Exception $e){
                flashToast("message","data Import Error");
                return redirect()->back('/home');
            }
        }else{
            return view("importStateCity");
        }
    }

    /**
     * City
     */

    public function cityList(Request $request){
       if($request->ajax()){
            $data = $this->_M_CityStateMap
            ->orderBy("state_id","ASC")
            ->get()->map(function($val){
                $val->state_name = $val->getState()->first()->state_name??"";
                return $val;
            });
            if ($request->has('export')) {
                $columns = json_decode($request->export_columns, true);
                $headings = json_decode($request->export_headings,true);
                if(!$headings){
                    $headings = collect($columns)->map(function ($col) {
                        return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                    })->toArray();
                }
                if ($request->export === 'excel') {
                    return Excel::download(new DataExport($data, $headings,$columns), 'City-list.xlsx');
                }
            }
            return DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('action', function ($val) {
                    $button = '<i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('.$val->id.')" ></i>';
                    $btn2 ='<i class="bi bi-lock-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('."'Are you sure you want to lock this item?'".', function() { deactivate('.$val->id.'); })" ></i>';
                    if($val->lock_status){
                        $btn2 ='<i class="bi bi-unlock-fill btn btn-sm" style ="color:rgb(37, 229, 37)" onclick="showConfirmDialog('."'Are you sure you want to unlock this item?'".', function() { activate('.$val->id.'); })" ></i>';
                    }
                    return $button.$btn2;
                })
                ->rawColumns(['action'])
                ->make(true);
       }
       $data["stateList"] = $this->_M_Sate->getStateOrm()->orderBy("state_name","ASC")->get();
       return view("Master/city_list",$data);
    }

    public function cityAddEdit(Request $request){
        try{
            DB::beginTransaction();
            $message = "New State Add";
            if($request->id){
                $this->_M_CityStateMap->edit($request);
                $message = "State Update";
            }else{
                $this->_M_CityStateMap->store($request);
            }
            DB::commit();
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function cityDtl($id){
        try{
            $data = $this->_M_CityStateMap->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function activeDeactivateCity($id,Request $request){
        try{
            $message="State Unlock";
            if($request->lock_status){
                $message="State Locked";
            }
            $request->merge(["id",$id]);
            $this->_M_CityStateMap->edit($request);
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    public function getCityListByState(Request $request){
        try{
            $data = $this->_M_CityStateMap->getCityOrm()->where("state_id",$request->id)->orderBy("city_name","ASC")->get();
            return responseMsgs(true,"data",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    /**
     * rollShortageLimit
     */

    public function rollShortageLimitList(Request $request){
        if($request->ajax()){
            $data =DB::select("                        
                        select rolls.*,qtm.quality,COALESCE(rsl.min_limit,0) as min_limit,rsl.lock_status,rsl.id,
                            COALESCE(stock.total_net_weight,0) as total_net_weight,stock.total_roll,stock.total_length,
                            transit.total_net_weight as transit_total_net_weight,transit.total_roll as transit_total_roll,transit.total_length as transit_total_length
                        from (
                                (
                            
                                    select size,roll_color,gsm,quality_id  
                                    from roll_details
                                    group by size,roll_color,gsm,quality_id  
                                )
                                UNION(
                                    select size,roll_color,gsm,quality_id 
                                    from roll_transits
                                    where size>2
                                    group by size,roll_color,gsm,quality_id  
                                )
                            
                        )rolls
                        left join roll_quality_masters qtm on qtm.id = rolls.quality_id
                        ".($request->dashboardData ? "":" LEFT ")." join roll_shortage_limits rsl on rsl.roll_color = rolls.roll_color
                            and rsl.roll_size = rolls.size
                            and rsl.roll_gsm = rolls.gsm
                            and rsl.quality_type_id = rolls.quality_id
                            ".($request->dashboardData ? " and rsl.lock_status=false ":"")."
                        left join(
                            select sum(net_weight) as total_net_weight,count(id) as total_roll,sum(length) as total_length,
                                size,roll_color,gsm,quality_id
                            from roll_details
                            where is_cut=false and is_roll_sell=false
                            group by size,roll_color,gsm,quality_id 
                        ) as stock on stock.roll_color = rolls.roll_color
                            and stock.size = rolls.size
                            and stock.gsm = rolls.gsm
                            and stock.quality_id = rolls.quality_id
                        left join(
                            select sum(net_weight) as total_net_weight,count(id) as total_roll,sum(length) as total_length,
                                size,roll_color,gsm,quality_id
                            from roll_transits
                            where is_cut=false and is_roll_sell=false
                                AND size>2
                            group by size,roll_color,gsm,quality_id 
                        )as transit on transit.roll_color = rolls.roll_color
                            and transit.size = rolls.size
                            and transit.gsm = rolls.gsm
                            and transit.quality_id = rolls.quality_id
                        order by rolls.size,rolls.roll_color,rolls.gsm,rolls.quality_id,rsl.id
            ");
            if($request->dashboardData){
                return responseMsgs(true,"data Fetched",$data);
            }
            return DataTables::of($data)
                ->addIndexColumn()             
                
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/rollShortageLimitList");
    }

    public function rollShortageLimitAddEdit(Request $request){
        try{
            DB::beginTransaction();
            $message = "Limit Update";
            $this->_M_RollShortageLimit->store($request);
            DB::commit();
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollShortageLimitActiveDeactivate(Request $request){
        try{
            DB::beginTransaction();
            $message = "Limit Lock";
            if(!$request->lock_status){
                $message="Limit Unlock";
            }
            $this->_M_RollShortageLimit->edit($request);
            DB::commit();
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
}
