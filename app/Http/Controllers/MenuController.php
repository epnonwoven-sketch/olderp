<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MenuMaster;
use App\Models\MenuPermission;
use App\Models\User;
use App\Models\UserTypeMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class MenuController extends Controller
{
    /**
     * ===============user for menu crud=================
     *          created by : Sandeep Bara
     */

    private $_M_UserTypeMaster;
    private $_M_MenuMaster;
    private $_M_MenuPermission;

    private $_menuTreeIds=[];
    public function __construct()
    {
        $this->_M_UserTypeMaster = new UserTypeMaster();
        $this->_M_MenuMaster = new MenuMaster();
        $this->_M_MenuPermission = new MenuPermission();
    }

    public function add(Request $request){
        try{
            DB::beginTransaction();
            $data["menu_type"] = 2;
            if($request->url_path==""){
                $data["url_path"]="#";
            }
            if ($request->parent_menu_mstr_id==-1) {
                $data["menu_type"] = 0;
            } else if ($request->parent_menu_mstr_id==0) {
                $data["menu_type"] = 0;
            } else if ($request->parent_menu_mstr_id!=0 && $request->parent_sub_menu_mstr_id==0 && $request->url_path=="") {
                $data["menu_type"] = 1;
            }
            if ($request->parent_sub_menu_mstr_id!=0) {
                $data["parent_menu_mstr_id"] = $request->parent_sub_menu_mstr_id;
            } else if ($request->parent_sub_menu_mstr_id!=0) {
                $data["parent_menu_mstr_id"] = $request->parent_menu_mstr_id;
            }
            $request->merge($data);

            if(!$request->id){                
                $menuId = $this->_M_MenuMaster->store($request);
                if($menuId)
                {
                    $user_type_mstr_id = $request->user_type_mstr_id;
                    $len = sizeof($user_type_mstr_id);
                    foreach($request->user_type_mstr_id as $id)
                    {
                        $data = [
                            'menu_master_id' => $menuId,
                            'user_type_master_id' => $id,
                            'created_on'=>date('Y-m-d H:i:s')
                        ];
                        $newRequest = new Request($data);
                        $model_menu_permission_id = $this->_M_MenuPermission->store($newRequest);
                    }
                    $data = [
                        'menu_master_id' => $menuId,
                        'user_type_master_id' => 1,
                        'created_on'=>date('Y-m-d H:i:s')
                    ];
                    $newRequest = new Request($data);
                    $model_menu_permission_id = $this->_M_MenuPermission->store($newRequest);
                } 
            }
            else{
                $menuId = $request->id;
                $this->_M_MenuMaster->edit($request);
                $this->_M_MenuPermission->where("menu_master_id",$menuId)->where("user_type_master_id","<>",1)->update(["lock_status"=>true]);
                
                foreach($request->user_type_mstr_id as $id)
                {
                    $data = [
                        'menu_master_id' => $menuId,
                        'user_type_master_id' => $id,
                        'created_on'=>date('Y-m-d H:i:s')
                    ];
                    $newRequest = new Request($data);
                    $model_menu_permission_id = $this->_M_MenuPermission->store($newRequest);
                }
                if(!$this->_M_MenuPermission->where("menu_master_id",$menuId)->where("user_type_master_id",1)->count()){
                    $data = [
                        'menu_master_id' => $menuId,
                        'user_type_master_id' => 1,
                        'created_on'=>date('Y-m-d H:i:s')
                    ];
                    $newRequest = new Request($data);
                    $model_menu_permission_id = $this->_M_MenuPermission->store($newRequest);
                }
            }
            DB::commit();
            return responseMsg(true,"New Menu Added",["id"=>$menuId]);
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivate($id,Request $request){
        try{
            $request->merge(["id",$id]);
            DB::enableQueryLog();
            $this->_M_MenuMaster->edit($request);
            return responseMsgs(true,"Menu Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    public function getSubMenuList(Request $request){
        try{
            $result  = $this->_M_MenuMaster->subMenuOrm()->where("parent_menu_mstr_id",$request->id)->get();
            $option = [];
            $status = true;

            if ($result) {
                $option = '<option value="0">#</option>';
                foreach ($result AS $list) {
                    $option .= '<option value="'.$list['id'].'" >'.$list['menu_name'].'</option>';
                }

            }
            $data["response"] = $option;
            return responseMsgs($status,"subMenuList",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function getMenuList(Request $request){
        try{
            $user = Auth()->user();
            $type_mstr_id = $user["user_type_id"];

            if($type_mstr_id!="2" && $type_mstr_id!="1")
            {
                return redirect()->to('/home');
            }
            if($request->ajax()){
                $data = $this->_M_MenuMaster->where("lock_status",false)->orderBy("id","ASC");
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('menu_icon', function ($val) {
                        return '<i class="'.($val["menu_icon"]? $val["menu_icon"] :"lni lni-grid-alt").'"></i>';
                    })
                    ->addColumn('parent_menu', function ($val) {
                        $menuTab = "";
                        $parenId = $val->parent_menu_mstr_id;
                        while(true){
                            $parent = $this->_M_MenuMaster->find($parenId);
                            if(!$parent){
                                break;
                            }
                            $parenId = $parent->parent_menu_mstr_id;
                            $menuTab.=("->").$parent->menu_name??"";
                        }
                        $menuTab = implode("->",array_reverse(explode("->",$menuTab)));
                        return trim($menuTab,"->");
                    })
                    ->addColumn('role_name', function ($val) {
                        // Replace this with your logic to get the role_name
                        return collect($val->getUserTypeList()->get())->implode("user_type",",");
                    })
                    ->addColumn('action', function ($val) {
                        return '<i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('.$val->id.')" ></i>
                               <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onClick="deactivateMenu('.$val->id.')" ></i>';
                    })->rawColumns(['menu_icon', 'action'])
                    ->make(true);
            }
            $data["user_type_list"] = $this->_M_UserTypeMaster->where("lock_status",false)->where("id","<>",1)->get();
            $data['underMenuNameList'] = $this->_M_MenuMaster->where("parent_menu_mstr_id",0)->where('menu_type',0)->where("lock_status",false)->get();
            return view("Menu/list",$data);
        }catch(Exception $e){
            return redirect()->back();
        }
    }

    public function getMenuDtl($id,Request $request){
        try{
            $data = $this->_M_MenuMaster->find($id);
            $parentMenu = $this->_M_MenuMaster->where("id",$data->parent_menu_mstr_id)->first();
            while($parentMenu && !in_array($parentMenu->parent_menu_mstr_id,[0,-1,1])){
                $parentMenu = $this->_M_MenuMaster->where("id",$parentMenu->parent_menu_mstr_id)->first();
            }
            $data->sub_parent_id = $parentMenu ? $data->parent_menu_mstr_id : 0;
            $data->parent_menu_mstr_id = $parentMenu ? $parentMenu->id : $data->parent_menu_mstr_id ;
            $data->user_type_mstr_id = $data->getUserTypeList()->get();
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function updateMenuUserTypeList(Request $request){
        if($request->ajax()){
            $data = $this->_M_UserTypeMaster->where("lock_status",false)->orderBy("id","ASC");
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return '<button class="btn btn-sm btn-info" onClick="updateMenu('.$val->id.')" >Update</button>';
                })->rawColumns(['menu_icon', 'action'])
                ->make(true);
        }
        return view("Menu/userType");
    }
    public function updateMenuByUserType(Request $request){
        try{
            $userType = $this->_M_UserTypeMaster->find($request->id);
            $pemitedMenu = $userType->getMenuList();
                            
            $menuId = $pemitedMenu->unique("menu_master_id")->pluck("menu_master_id");
            $menus = $this->_M_MenuMaster->whereIn("id",$menuId)
                                            ->where("lock_status",false)
                                            ->get();                                        
            $tree = $this->generateMenuTree($menus); 
            Redis::set("menu_list_".$userType->id,$tree);
            return responseMsgs(true,"Menu Update","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function generateMenuTree($data,$parenId=0){       
        $data = collect($data); 
        
                 
        $parent = $data->where("parent_menu_mstr_id",$parenId)
                ->whereNotNull("parent_menu_mstr_id")
                ->sortBy(["parent_menu_mstr_id","order_no"]); 
              
        $tree =  $parent->map(function($val,$index)use($data){
            $this->_menuTreeIds[]=$val["id"];
            if($val["id"])
            return [
                "menu_name"=>$val["menu_name"]??"",
                "url_path"=>$val["url_path"]??"",
                "query_string"=>$val["query_string"]??"",
                "menu_icon"=>$val["menu_icon"]??"",
                "description"=>$val["description"]??"",
                "id"=>$val["id"],
                "parent_menu_mstr_id"=>$val["parent_menu_mstr_id"],                
                "order_no"=>$val["order_no"],
                "childe"=>$this->generateMenuTree($data,$val["id"]),
            ];

        })->values(); 
        if($parenId==0){
            $extraMenu =($data->whereNotIn("id",$this->_menuTreeIds));
            $extMenu = $extraMenu->map(function($val)use($extraMenu){
                $this->_menuTreeIds[]=$val["id"];
                return[
                    "menu_name"=>$val["menu_name"]??"",
                    "url_path"=>$val["url_path"]??"",
                    "query_string"=>$val["query_string"]??"",
                    "menu_icon"=>$val["menu_icon"]??"",
                    "description"=>$val["description"]??"",
                    "id"=>$val["id"],       
                    "parent_menu_mstr_id"=>$val["parent_menu_mstr_id"],       
                    "order_no"=>$val["order_no"],
                    "childe"=>$this->generateMenuTree($extraMenu,$val["id"]),
                ];
            })->values();
            $tree=$tree->merge($extMenu);

            $itemsByReference = array();

            foreach ($tree as $key => &$item) {
                $itemsByReference[$item['id']] = &$item;
            }
            foreach ($tree as $key => &$item) {
                if ($item['id'] && isset($itemsByReference[$item['parent_menu_mstr_id']])){
                    $itemsByReference[$item['id']][] = &$item;
                }
    
                # to remove the external loop of the child node ie. not allowing the child node to create its own treee
                if ($item['parent_menu_mstr_id'] && isset($itemsByReference[$item['parent_menu_mstr_id']]))
                    unset($tree[$key]);
            }
        } 
        return $tree->sortBy("order_no")->values();
    }
    

}
