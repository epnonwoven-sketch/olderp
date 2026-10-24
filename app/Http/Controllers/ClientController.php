<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Models\CityStateMap;
use App\Models\ClientDetailMaster;
use App\Models\Sector;
use App\Models\StateMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends Controller
{
//
    private $_M_ClientDetail;
    private $_M_Sector;
    function __construct()
    {
        $this->_M_ClientDetail = new ClientDetailMaster();
        $this->_M_Sector = new Sector();
    }

    public function addClient(Request $request){
        try{
            $rule = [
                "id"=>"nullable".($request->id?("|exists:".$this->_M_ClientDetail->getTable().",id"):""),
                "clientName"=>"required|unique:".$this->_M_ClientDetail->getTable().",client_name".($request->id?(",".$request->id.",id"):""),
                "mobileNo"=>"required",
                "stateId"=>"required",
                "cityId"=>"required",
                "sectorId"=>"required|exists:".$this->_M_Sector->getTable().",id",
                "paymentTerm"=>"required|in:advance,credit,others",
                "creditDays"=>"required_if:paymentTerm,credit|nullable|integer|min:1",
                "paymentTermNote"=>"required_if:paymentTerm,others|nullable|string|max:255",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }
            $request->merge(["hasAddressTwo"=>$request->hasAddressTwo ? true:false]);
            if($request->paymentTerm!=="credit"){
                $request->merge(["creditDays"=>null]);
            }
            if($request->paymentTerm!=="others"){
                $request->merge(["paymentTermNote"=>null]);
            }
            $id = $request->id;
            if($request->id){
                $this->_M_ClientDetail->edit($request);
            }else{
                $id =  $this->_M_ClientDetail->store($request);
            }
            $data["client"]=$this->_M_ClientDetail->find($id);
            return responseMsgs(true,"New Client Add",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function clientList(Request $request){
        try{
            if($request->ajax()){
                $clients = $this->_M_ClientDetail->getClientListOrm()
                        ->orderBy("id","ASC")
                        ->get();
                $sectors = Sector::whereIn("id", $clients->pluck("sector_id")->filter()->unique()->values())->get()->keyBy("id");
                $cities = CityStateMap::whereIn("id", $clients->pluck("city_id")->filter()->unique()->values())->get()->keyBy("id");
                $states = StateMaster::whereIn("id", $clients->pluck("state_id")->filter()->unique()->values())->get()->keyBy("id");
                $data = $clients->map(function($val) use ($sectors, $cities, $states){
                    $sector = $sectors->get($val->sector_id);
                    $city = $cities->get($val->city_id);
                    $state = $states->get($val->state_id);
                    $val->sector = $sector->sector ?? "";
                    $val->city_name = $city->city_name ?? "";
                    $val->state_name = $state->state_name ?? "";
                    $val->payment_terms_label = $val->paymentTermLabel();
                    return $val;
                });
                if ($request->has('export')) {
                    $columns = json_decode($request->export_columns, true);
                    $headings = json_decode($request->export_headings,true);
                    if(!$headings){
                        $headings = collect($columns)->map(function ($col) {
                            return ucwords(str_replace('_', ' ', $col));
                        })->toArray();
                    }
                    if ($request->export === 'excel') {
                        return Excel::download(new DataExport($data, $headings,$columns), 'Client-list.xlsx');
                    }
                }
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn("state_name",function($val){
                        return $val->state_name;
                    })
                    ->addColumn("city_name",function($val){
                        return $val->city_name;
                    })
                    ->addColumn('action', function ($val) {
                        return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                          EOD;
                    })->rawColumns(['action'])
                    ->make(true);
            }
            return view("Client/list");
        }catch(Exception $e){
            flashToast("message","Internal Server Error");
            return redirect()->back();
        }
    }

    public function getClientDtl($id,Request $request){
        try{
            $data = $this->_M_ClientDetail->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivate($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_ClientDetail->edit($request);
            return responseMsgs(true,"Client Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

}
