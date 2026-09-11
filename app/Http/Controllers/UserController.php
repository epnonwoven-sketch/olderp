<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Http\Controllers\MenuController;
use App\Models\MenuMaster;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Validate;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    //
    protected $_M_User;
    protected $_M_MenuMaster;

    function __construct()
    {
        $this->_M_User = new User();
        $this->_M_MenuMaster = new MenuMaster();
    }

    public function hasPassword(Request $request){
        try{
            DB::enableQueryLog();
            $datas =  $this->_M_User::select('id', 'password')
                ->where('password',12345)
                ->orderby('id')
                ->get();
            DB::beginTransaction();
            foreach ($datas as $data) {
                $user = User::find($data->id);     
                $user->password = Hash::make(12345);
                $user->update();
                
            }
            DB::commit();
        }catch(Exception $e){
            dd("jjj");
        }
    }

    public function login(Request $request){
        try{ 
            $data = $request->all();
            if($request->getMethod()=="GET"){
                return view("User/login",$data);
            }
            elseif($request->getMethod()=="POST"){
                
                $validate = Validator::make($request->all(),
                    [
                        'email' => 'required|email',
                        'password' => 'required|confirmed',
                    ]
                    );
                if($validate->failed()){                    
                    return redirect()->back()
                        ->withErrors($validate->failed())
                        ->withInput();
                }
                
                if($user = $this->_M_User->where("email",$request->email)->first()){
                    
                    if(!(Hash::check($request->password, $user->password))){
                        flashToast("message","Invalid User");
                        return redirect()->back()
                            ->withErrors(["email"=>"Invalid password!.."])
                            ->withInput();
                    }
                    $credentials = $request->only('email', 'password');

                    
                    if (Auth::attempt($credentials)) {
                        $menuList ="";Redis::get("menu_list_".$user["user_type_id"]);                        
                        if (!$menuList) {
                            $pemitedMenu = $user->getMenuList()->get();
                            
                            $menuId = $pemitedMenu->unique("menu_master_id")->pluck("menu_master_id");
                            $menus = $this->_M_MenuMaster->whereIn("id",$menuId)
                                                            ->where("lock_status",false)
                                                            ->get();                                        
                            $tree = (new MenuController())->generateMenuTree($menus); 
                            Redis::set("menu_list_".$user["user_type_id"],$tree);
                        }
                        session(['last_activity' => Carbon::now()]);
                        flashToast("message","Login");
                        return redirect()->to('/home');
                    }
                }else{
                    flashToast("message","Invalid User");
                    return redirect()->back()
                        ->withErrors(["email"=>"Invalid Email Id!.."])
                        ->withInput();
                }
            }
            
        }catch(Exception $e){
            flashToast("message","Internal Server Error");
            return redirect()->back();
        }

    }

    /**
     * Log the user out.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        Auth::guard('web')->logout();

        // Auth::logout();
        return redirect()->route('login');
    }

    public function profile(){
        return redirect()->back();
    }
    public function changePassword(){
        return redirect()->back();
    }

    public function createUser(Request $request){
        try{
            $rule = [
                "name"=>"required",
                "password"=>"required|confirmed",
                "email"=>"required|email|unique:".$this->_M_User->getTable().",email"
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){                    
                return validationError($validate);
            }
            $request->merge(["password"=>Hash::make($request->password)]);
            $this->_M_User->store($request);
            return responseMsgs(true,"New User Added","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function editUser(Request $request){
        try{
            $rule = [
                "id"=>"required|exists:".$this->_M_User->getTable().",id",
                "name"=>"required",
                "email"=>"required|email|unique:".$this->_M_User->getTable().",email,".$request->id.",id" ,
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){                    
                return validationError($validate);
            }
            if($request->password){
                $request->merge(["password"=>Hash::make($request->password)]);
            }
            $this->_M_User->edit($request->id,$request);
            return responseMsgs(true,"New User Added","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function userList(Request $request){
        $user_type = Auth()->user()->user_type_id??"";
        if($request->ajax()){
            $data = $this->_M_User
                    ->select("users.*","user_type_masters.user_type")
                    ->leftJoin("user_type_masters","user_type_masters.id","users.user_type_id");
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
                    return Excel::download(new DataExport($data, $headings,$columns), 'User-list.xlsx');
                }
            }
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) use($user_type) {                    
                    $button = "";
                    if($val->is_roll_cut){
                        return $button;
                    }
                    if(in_array($user_type,[1,2])){
                        $button .= '<button class="btn btn-sm btn-warning" onClick="userEditModal('.$val->id.')" >Edit</button>';
                    }if(in_array($user_type,[1,2]) && $val->id !=1){
                        $button .= '<button class="btn btn-sm btn-danger" onClick="suspendUser('.$val->id.')" >Suspend</button>';
                    }
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
                // dd(DB::getQueryLog());
            return $list;

        }
        return view("User.list");
    }

    public function userDtl(Request $request){
        try{
            $user = $this->_M_User->find($request->id);
            return responseMsgs(true,"user Fetched",$user);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
}
