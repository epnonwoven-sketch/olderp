<?php

namespace App\Http\Controllers;

use App\Models\Sector;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class SectorController extends Controller
{
    
    private $_M_Sector;

    function __construct()
    {
        $this->_M_Sector = new Sector();
    }

    public function addEditSector(Request $request){
        try{
            $rules = [
                "id"=>"nullable".($request->id? ("|exists:".$this->_M_Sector->getTable().",id") :""),
                "sector"=>"required"
            ];
            $validator = Validator::make($request->all(),$rules);
            if($validator->fails()){
                return validationError($validator);
            }
            $id = $request->id;
            $message = "New Sector Add";
            if($request->id){
                $this->_M_Sector->edit($request);
                $message ="Sector Edit";
            }
            else{
                $id = $this->_M_Sector->store($request);
            }            
            $data["sector"]=$this->_M_Sector->find($id);
            return responseMsgs(true,$message,$data);

        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function sectorList(Request $request){
        try{
            if($request->ajax()){
                $data = $this->_M_Sector->getSectorListOrm()->orderBy("id","ASC")->get();
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($val) {
                        return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                        EOD;
                    })->rawColumns(['action'])
                    ->make(true);
            }
            return view("Sector/list");
        }catch(Exception $e){
            flashToast("message","Internal Server Error");
            return redirect()->back();
        }
    }

    public function deactivate($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_Sector->edit($request);
            return responseMsgs(true,"Sector Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    public function getSectorDtl($id,Request $request){
        try{
            $data = $this->_M_Sector->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
}
