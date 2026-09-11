<?php

namespace App\Http\Controllers;

use App\Models\BagTypeMaster;
use App\Models\ClientDetailMaster;
use App\Models\GarbageEntry;
use App\Models\MachineMater;
use App\Models\OrderBroker;
use App\Models\OrderPunchDetail;
use App\Models\OrderRollBagType;
use App\Models\RollDetail;
use App\Models\RollQualityMaster;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    //

    protected $_M_Machine;
    protected $_M_RollDetails;
    protected $_M_OrderPunches;
    protected $_M_OrderRollBagType;
    protected $_M_Brokers;
    protected $_M_User;
    protected $_M_GarbageEntry;
    protected $_M_ClientDetails;
    protected $_M_BagType;

    function __construct()
    {
        $this->_M_Machine = new MachineMater();
        $this->_M_RollDetails = new RollDetail();
        $this->_M_OrderPunches = new OrderPunchDetail();
        $this->_M_OrderRollBagType = new OrderRollBagType();
        $this->_M_Brokers = new OrderBroker();
        $this->_M_User = new User();
        $this->_M_GarbageEntry = new GarbageEntry();
        $this->_M_ClientDetails = new ClientDetailMaster();
        $this->_M_BagType = new BagTypeMaster();
    }

    public function dailyProduction(Request $request){
        $machine=$this->_M_Machine->orderBy("is_printing", "DESC")
                                ->orderBy("id", "ASC")
                                ->get();
        $machine->map(function($val){
            $monthly =0;
            $weakly =0;
            $daily =0;
            $todayProduction=[];
            $currentDate = Carbon::now();
            $today = $currentDate->copy()->format("Y-m-d");
            $fromYear = $currentDate->copy()->subYear()->format("Y-m-d");
            $fromMonth = $currentDate->copy()->subDays(35)->format("Y-m-d");
            $fromWeak = $currentDate->copy()->subWeek()->format("Y-m-d");
            if($val->is_cutting){
                $monthly = $this->_M_RollDetails->where("cutting_machine_id",$val->id)->whereBetween("cutting_date",[$fromYear,$today])->count()/12;
                $weakly = $this->_M_RollDetails->where("cutting_machine_id",$val->id)->whereBetween("cutting_date",[$fromMonth,$today])->count()/5;
                $daily = $this->_M_RollDetails->where("cutting_machine_id",$val->id)->whereBetween("cutting_date",[$fromWeak,$today])->count()/7;
                $todayProduction = $this->_M_RollDetails->where("cutting_machine_id",$val->id)->where("cutting_date",$today)->orderBy("updated_at","ASC")->get();
            }elseif($val->is_printing){
                $monthly = $this->_M_RollDetails->where("printing_machine_id",$val->id)->whereBetween("printing_date",[$fromYear,$today])->count()/12;
                $weakly = $this->_M_RollDetails->where("printing_machine_id",$val->id)->whereBetween("printing_date",[$fromMonth,$today])->count()/5;
                $daily = $this->_M_RollDetails->where("printing_machine_id",$val->id)->whereBetween("printing_date",[$fromWeak,$today])->count()/7;
                $todayProduction = $this->_M_RollDetails->where("printing_machine_id",$val->id)->where("printing_date",$today)->orderBy("updated_at","ASC")->get();
            }
            $val->monthly = roundFigure($monthly);
            $val->weakly = roundFigure($weakly);
            $val->daily = roundFigure($daily);
            $val->todayProduction = collect($todayProduction)->map(function($val){
                $val->purchase_date = $val->purchase_date ? Carbon::parse($val->purchase_date)->format("d-m-Y"):"";
                $val->bag_size = (float)$val->w." x ".(float)$val->l.($val->g ?(" x ".(float)$val->g) :"") ;
                $val->client_name = $val->getClient()->first()->client_name??"";
                $val->vendor_name = $val->getVendor()->first()->vendor_name??"";
                $val->bag_type = $val->getBagType()->first()->bag_type??"";
                $val->quality = $val->getQualityType()->first()->quality??"";
                return $val;
            });
        });
        $data["machineList"] = $machine;
        return view("Reports/daily_production",$data);
    }

    public function orderRepitition(Request $request){
        if($request->ajax()){
            $currentDate = Carbon::now();
            $fromDate = $currentDate->copy()->subMonths(2)->format('Y-m-d');
            $uptoDate = $currentDate->copy()->format("Y-m-d");
            DB::enableQueryLog();
            $data = DB::select("
                        SELECT 
                            btm.bag_type, 
                            opd.bag_quality, 
                            opd.total_units,
                            opd.bag_w, opd.bag_l, opd.bag_g, cdm.client_name,
                            gsm.bag_gsm_value, 
                            COUNT(DISTINCT opd.id) AS count
                        FROM order_punch_details opd
                        LEFT JOIN client_detail_masters cdm ON cdm.id = opd.client_detail_id
                        LEFT JOIN bag_type_masters btm on btm.id = opd.bag_type_id
                        CROSS JOIN LATERAL jsonb_array_elements_text(opd.bag_gsm::jsonb) AS gsm(bag_gsm_value) -- Fix JSON parsing
                        WHERE opd.order_date BETWEEN ? AND ?
                            AND opd.lock_status = ?
                        GROUP BY 
                            btm.bag_type, 
                            opd.bag_quality, 
                            opd.total_units,
                            opd.bag_w, opd.bag_l, opd.bag_g, cdm.client_name,
                            gsm.bag_gsm_value
                        HAVING COUNT(*) > 1
                        ORDER BY COUNT(*) DESC;
                    ", [$fromDate, $uptoDate, false]);
                    $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('bag_size', function ($val) { 
                        return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                    })
                    ->rawColumns(['row_color', 'action'])
                    ->make(true);
                return $list;
        }
        $data=[];
        return view("Reports/orderRepitition",$data);
    }

    public function agentOrder(Request $request){

        if($request->ajax())
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $agentIds = $request->agentId;
            $data = $this->_M_Brokers->select("order_brokers.broker_name","order_brokers.id",
                        DB::raw("
                                count(order_punch_details.id) as total_order,
                                count(case when order_punch_details.is_delivered then order_punch_details.id end) as order_delivered
                            "
                        )
                    )
                    ->leftJoin("order_punch_details",function($join) use($fromDate,$uptoDate){
                        $join->on("order_punch_details.broker_id","order_brokers.id")
                        ->where("order_punch_details.lock_status",false);
                        if($fromDate && $uptoDate){
                            $join->whereBetween("order_punch_details.order_date",[$fromDate,$uptoDate]);
                        }
                        elseif($fromDate){
                            $join->where("order_punch_details.order_date",$fromDate);
                        }
                        elseif($uptoDate){
                            $join->where("order_punch_details.order_date",$uptoDate);
                        }
                    })
                    ->where("order_brokers.lock_status",false)
                    ->groupBy("order_brokers.id","order_brokers.broker_name")
                    ->orderBy(DB::raw("total_order"),"DESC");
            if($agentIds){
                $data->where("order_brokers.id",$agentIds);
            }
            $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($val){

                        $route = route('report.agent.order.dtl');
                        $queryParams = http_build_query(request()->all()); // Convert array to query string

                        return $url = <<<EOD
                        <a href="{$route}?agentId={$val->id}&{$queryParams}">View</a>
                        EOD;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            return $list;
            
        }
        $data=[];
        $data["agentList"] = $this->_M_Brokers->getBrokerOrm()->get();
        return view("Reports/agentOrder",$data);
    }

    public function agentOrderDtl(Request $request){
        if($request->ajax()){
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $agentIds = $request->agentId;
            DB::enableQueryLog();
            $data = $this->_M_OrderPunches->select("order_punch_details.*","order_brokers.broker_name","client_detail_masters.client_name")
                    ->Join("order_brokers","order_brokers.id","order_punch_details.broker_id")
                    ->join("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")
                    ->where("order_punch_details.lock_status",false)
                    ->orderBy("order_punch_details.id","DESC");
            if($fromDate && $uptoDate){
                $data->whereBetween("order_punch_details.order_date",[$fromDate,$uptoDate]);
            }
            elseif($fromDate){
                $data->where("order_punch_details.order_date",$fromDate);
            }
            elseif($uptoDate){
                $data->where("order_punch_details.order_date",$uptoDate);
            }
            if($agentIds){
                $data->where("order_punch_details.broker_id",$agentIds);
            }
            $data = $data->get();
            $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn("order_date",function($val){
                        return $val->order_date ? Carbon::parse($val->order_date)->format("d-m-Y"):"";
                    })
                    ->addColumn('bag_size', function ($val) { 
                        return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                    })
                    ->addColumn('action', function ($val){
                        return "";
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            return $list;
        }
        $data = $request->all();        
        $data["agentList"] = $this->_M_Brokers->getBrokerOrm()->get();
        return view("Reports/agentOrderDtl",$data);
    }

    public function legacyClientOrder(Request $request){
        if($request->ajax()){
            $fromDate = $request->fromDate;
            $data = DB::select("
                SELECT *
                FROM (
                    SELECT cdm.*, opd.order_date, opd.client_detail_id, opd.bag_type_id, bgt.bag_type,
                            opd.bag_quality,opd.bag_gsm, opd.total_units, opd.units , 
                            opd.bag_w, opd.bag_l , opd.bag_g , opd.bag_color,
                            ROW_NUMBER() OVER (PARTITION BY cdm.id ORDER BY opd.id DESC) AS rn
                    FROM client_detail_masters cdm
                    LEFT JOIN order_punch_details opd 
                        ON opd.client_detail_id = cdm.id
                    LEFT JOIN bag_type_masters bgt ON bgt.id = opd.bag_type_id
                    WHERE cdm.lock_status = FALSE
                ) t
                WHERE rn = 1 and (
                ".($fromDate ? " order_date<='2025-03-01' or order_date is null " : " order_date is null ")."
                )
                ORDER BY order_date ASC
            ");
            $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn("order_date",function($val){
                        return $val->order_date ? Carbon::parse($val->order_date)->format("d-m-Y"):"";
                    })
                    ->addColumn('bag_size', function ($val) { 
                        return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                    })
                    ->addColumn('action', function ($val){
                        return "";
                    })
                    ->rawColumns(['action'])
                    ->with(["fromDate"=>$fromDate?Carbon::parse($fromDate)->format("d-m-Y"):""])
                    ->make(true);
            return $list;
        }
        return view("Reports/legacyClientOrder");
    }

    public function rollShortage(Request $request){
        if($request->ajax()){

        }
        $data=[];
        return view("Reports/rollShortage",$data);
    }

    public function garbageRegister(Request $request){
        if($request->ajax())
        {
            $user = $this->_M_User->all();
            $machine = $this->_M_Machine->all();DB::enableQueryLog();
            $data = $this->_M_GarbageEntry->select("garbage_entries.*","c.client_name",
                        "order_punch_details.bag_w","order_punch_details.bag_l","order_punch_details.bag_g",
                        "resistor.roll_no",
                        DB::raw("((garbage/ (CASE WHEN roll_weight=0 THEN 1 ELSE roll_weight END))*100) AS garbage_per")
                    )
                    ->join("client_detail_masters as c","c.id","garbage_entries.client_id")
                    ->leftJoin("order_punch_details","order_punch_details.id","garbage_entries.order_id")
                    ->leftJoin(DB::raw("(
                        select garbage_accept_registers.garbage_entry_id,
                            string_agg(distinct(rolls.roll_no),',') as roll_no
                        from garbage_accept_registers
                        join (
                                select id,roll_no
                                from roll_details
                                union all (
                                    select id,roll_no
                                    from roll_transits
                                )
                            ) as rolls on rolls.id = garbage_accept_registers.roll_id
                        where garbage_accept_registers.lock_status = false
                        group by garbage_accept_registers.garbage_entry_id
                    ) as resistor"),"resistor.garbage_entry_id","garbage_entries.id")
                    ->where("garbage_entries.lock_status",false)
                    ->where("garbage_entries.is_verify",true);
            if($request->fromDate && $request->uptoDate){
                $data->whereBetween("garbage_entries.cutting_date",[$request->fromDate,$request->uptoDate]);
            }elseif($request->fromDate){
                $data->where("garbage_entries.cutting_date","<=",$request->fromDate);
            }elseif($request->uptoDate){
                $data->where("garbage_entries.cutting_date",">=",$request->uptoDate);
            }
            $data = $data->get()
                    ->map(function($val) use($user,$machine){
                        $val->bag_size = (float)$val->bag_w." X ".(float)$val->bag_l.($val->bag_g ? (" X ".(float)$val->bag_g):"");
                        $val->operator_name = $user->where("id",$val->operator_id)->first()->name??"";
                        $val->helper_name = $user->where("id",$val->helper_id)->first()->name??"";
                        $val->machine = $machine->where("id",$val->machine_id)->first()->name??""; 
                        $val->cutting_date = $val->cutting_date ? Carbon::parse($val->cutting_date)->format("d-m-Y"):"";
                        $val->percent = roundFigure($val->roll_weight ? ($val->garbage/$val->roll_weight)*100 :0)." %";
                        $val->wip_percent = roundFigure($val->roll_weight ? ($val->wip_disbursed_in_kg/$val->roll_weight)*100 :0)." %";
                        $val->total_garbage = roundFigure($val->garbage +  $val->wip_disbursed_in_kg);
                        $val->verify_by = $user->firstWhere("id",$val->verify_by)->name??"";
                        $val->verify_date = $val->verify_date ? Carbon::parse($val->verify_date)->format("d-m-Y"):"";
                        return $val;
                    })
                    ->sortBy([
                        ["total_garbage", "DESC"],
                        ["cutting_date", "asc"]
                    ]);
            $list = DataTables::of($data)
                ->addIndexColumn()  
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;
        }
        list($from,$upto) = explode("-",getFY());
        $data["fromDate"] = $from."-04-01";
        $data["uptoDate"] = Carbon::now()->format("Y-m-d");
        return view("Reports/garbage",$data);
    }

    /**
     * order data status
     */

    public function rollStatus(Request $request){
        if($request->ajax()){
            $data=$this->_M_RollDetails->select("roll_details.*","vendor_detail_masters.vendor_name")
                  ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                  ->where("roll_details.lock_status",false);
            if($request->uptoDate){
                $data->where(DB::raw("cast(roll_details.roll_receiving_at as date)"),"<=",$request->uptoDate)
                ->where(function($query)use($request){
                    $query->whereNull("roll_details.cutting_date")
                    ->orWhere("roll_details.cutting_date",">=",$request->uptoDate);
                });
            }
            $data = $data->orderBy("purchase_date","ASC")->get()->map(function($val)use($request){
                
                $val->printing_color = collect(json_decode($val->printing_color,true))->implode(",");
                $val->gsm_json = collect(json_decode($val->gsm_json,true))->implode(",");

                $val->purchase_date = $val->purchase_date ? Carbon::parse($val->purchase_date)->format("d-m-Y"):"";
                $val->printing_date = $val->printing_date && $val->printing_date<=$request->uptoDate ? $val->printing_date:null;
                $val->cutting_date = $val->cutting_date ? Carbon::parse($val->cutting_date)->format("d-m-Y"):"";
                $bookRoll = $this->_M_OrderRollBagType->where("roll_id",$val->id)->where(DB::raw("cast(created_at as date)"),"<=",$request->uptoDate)->orderBy("id","DESC")->first();
                $orderKeys=["client_detail_id","estimate_delivery_date","delivery_date","is_delivered","bag_type_id","bag_type","client_name","bag_unit","w","l","g","printing_color","is_printed","printing_date","weight_after_print","printing_machine_id","is_cut","cutting_date","weight_after_cutting","cutting_machine_id","loop_color"];
                if(!$bookRoll || !$val->client_detail_id){
                    foreach ($orderKeys as $key) {
                        $val->$key = null;
                    }
                }elseif($val->client_detail_id){
                    $val->client_name = $this->_M_ClientDetails->find($val->client_detail_id)->client_name??"";
                    $val->bag_type = $this->_M_BagType->find($val->bag_type_id)->bag_type??"";
                }
                return $val;
            });
            $summary=[
                "totalWeight"=>roundFigure($data->sum("net_weight")),
            ];
            $list = DataTables::of($data)
                ->addIndexColumn() 
                ->addColumn('row_color', function ($val) {
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
                ->addColumn('gsm_variation', function ($val) {                        
                    return roundFigure($val->gsm_variation)."%";
                })
                ->addColumn("grade",function($val){
                    $quality = RollQualityMaster::find($val->quality_id);
                    $grade = $quality ? $quality->getGrade()->first()->grade??"":"";
                    return $grade ;                        
                })
                ->addColumn("quality",function($val){
                    $quality = RollQualityMaster::find($val->quality_id);
                    return ($quality->quality??"").(" (".$val->hardness.")") ;                        
                })
                ->addColumn("bag_size",function ($val) {
                    return $val->bag_type_id ? ((float)$val->w." x ".(float)$val->l.($val->g?(" x ".(float)$val->g):"")):null;
                }) 
                ->rawColumns(['row_color', 'action'])
                ->with($summary)
                ->make(true);
            return $list;
        }
        $data=[];
        return view("Reports/rollStatus",$data);
    }
}
