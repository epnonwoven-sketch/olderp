<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Models\VendorDetailMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    //
    private $_M_VendorDetail;
    function __construct()
    {
        $this->_M_VendorDetail = new VendorDetailMaster();
    }

    public function addVendor(Request $request){
        try{
            if($request->id){
                $this->_M_VendorDetail->edit($request);
            }else{
                $this->_M_VendorDetail->store($request);
            }
            return responseMsgs(true,"New Vendor Add","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function vendorList(Request $request){
        try{
            if($request->ajax()){
                $data = $this->_M_VendorDetail->where("lock_status",false);
                if ($request->has('export')) {
                    $columns = json_decode($request->export_columns, true);
                    $headings = json_decode($request->export_headings,true);
                    if(!$headings){
                        $headings = collect($columns)->map(function ($col) {
                            return ucwords(str_replace('_', ' ', $col)); // Converts 'auto_name' => 'Auto Name'
                        })->toArray();
                    }
                    $data = $data->get();
                    if ($request->export === 'excel') {
                        return Excel::download(new DataExport($data, $headings,$columns), 'Vendor-list.xlsx');
                    }
                }
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($val) {

                        return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivateMenu('$val->id'); })" ></i>
                          EOD;
                        // return '<button class="btn btn-sm btn-primary" onClick="openModelEdit('.$val->id.')" >Edit</button>
                        //        <button class="btn btn-sm btn-info" onClick="deactivateMenu('.$val->id.')" >Delete</button>';
                    })->rawColumns(['menu_icon', 'action'])
                    ->make(true);
            }
            return view("Vendor/list");
        }catch(Exception $e){
            flashToast("message","Internal Server Error");
            Log::error('Error occurred: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back();
        }
    }
    
    public function getVenderDtl($id,Request $request){
        try{
            $data = $this->_M_VendorDetail->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivate($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_VendorDetail->edit($request);
            // dd(DB::getQueryLog());
            return responseMsgs(true,"Menu Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }
}
