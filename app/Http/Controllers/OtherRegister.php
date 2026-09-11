<?php

namespace App\Http\Controllers;

use App\Models\AutoDetail;
use App\Models\ClientDetailMaster;
use App\Models\GarbageAcceptRegister;
use App\Models\GarbageNotAcceptRegister;
use App\Models\LoopStock;
use App\Models\RateTypeMaster;
use App\Models\TransporterDetail;
use App\Models\VendorDetailMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OtherRegister extends Controller
{
    //

    protected $_M_GarbageAcceptRegister;
    protected $_M_GarbageNotAcceptRegister;
    protected $_M_LoopStock;
    function __construct()
    {
        $this->_M_GarbageAcceptRegister = new GarbageAcceptRegister();
        $this->_M_GarbageNotAcceptRegister = new GarbageNotAcceptRegister();
        $this->_M_LoopStock = new LoopStock();
    }

    public function acceptGarbage(Request $request){
        if($request->ajax()){
            $data = $this->_M_GarbageAcceptRegister
                    ->select("roll_details.*","garbage_accept_registers.*","users.name AS operator_name","helper.name AS helper_name")
                    ->join("roll_details","roll_details.id","garbage_accept_registers.roll_id")
                    ->leftJoin("users",function($join){
                        $join->on("users.id","garbage_accept_registers.operator_id")
                            ->where("users.user_type_id",6);
                    })
                    ->leftJoin("users AS helper",function($join){
                        $join->on("helper.id","garbage_accept_registers.helper_id")
                            ->where("helper.user_type_id",5);
                    })
                    ->where("garbage_accept_registers.lock_status",false)
                    ->orderBy("garbage_accept_registers.id","DESC");
            
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn("cutting_date",function($val){
                        return $val->cutting_date ? Carbon::parse($val->cutting_date)->format("d-m-Y"):"";
                    })
                    ->make(true);
        }
        return view("Register/accept_garbage");
    }

    public function loopStockBook(Request $request){
        $data["loop"] = $this->_M_LoopStock->select(DB::raw(
            "
            loop_stocks.loop_color,loop_stocks.balance,loop_stocks.min_limit, COALESCE(credit,0) as credit,COALESCE(debit,0) as debit,
            round(loop_stocks.balance + COALESCE(credit,0) - COALESCE(debit,0)) opening_balance,
            round(COALESCE(credit,0) - COALESCE(debit,0)) as book_loop
            "
        ))
        ->leftJoin(
            DB::raw(
                "
                (
                    select sum(credit) as credit , sum(debit) as debit, loop_stock_id
                    from loop_usage_accounts
                    join (
                        (
                            select id
                            from roll_details
                            where is_cut = false and is_roll_sell=false
                                and lock_status = false
                        )
                        UNION(
                            select id
                            from roll_details
                            where is_cut = false and is_roll_sell=false
                                and lock_status = false
                        )
                    )as roll on roll.id = loop_usage_accounts.roll_id
                    group by loop_stock_id
                ) as book
                "
            ),"book.loop_stock_id","loop_stocks.id"
        )
        ->orderBy("loop_stocks.loop_color","ASC")
        ->get();
        $data["total_balance"] = $data["loop"]->sum("balance");
        $data["autoList"] =AutoDetail::where("lock_status",false)->orderBy("id","ASC")->get();
        $data["transporterList"] = TransporterDetail::where("lock_status",false)->where("is_bus",false)->orderBy("id","ASC")->get();
        $data["rateType"] = RateTypeMaster::all();
        $data["clientList"] = (new ClientDetailMaster())->getClientListOrm()->where("id","<>",1)->orderBy("client_name","ASC")->get();
        $data["vendorList"] = (new VendorDetailMaster())->getVenderListOrm()->orderBy("vendor_name","ASC")->get();

        return view("Register/loop_stock_status",$data);
    }
}
