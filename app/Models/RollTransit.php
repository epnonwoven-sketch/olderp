<?php

namespace App\Models;

use App\Traits\Loggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RollTransit extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'roll_no',
        "purchase_date",
        "vender_id",
        "vehicle_no",
        "vendor_roll_no",
        "gsm",
        "gsm_json",
        "gsm_variation",
        "roll_color",
        "length",
        "size",
        "net_weight",
        "gross_weight",
        "hardness",
        "roll_type",
        "client_detail_id",
        "estimate_delivery_date",
	    "delivery_date"  ,
	    "is_delivered" ,
	    "bag_type_id",
	    "bag_unit" ,
        "w" ,
        "l" ,
        "g" ,
        "printing_color" ,
        "is_printed",
        "printing_date",
        "weight_after_print",
        "printing_machine_id",
        "is_cut",
        "cutting_date",
        "weight_after_cutting",
        "cutting_machine_id",
        "lock_status",
        "loop_color",
        "quality_id",
        "roll_receiving_at",
        "is_roll_sell",
        "split_role_id",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        if(!isset($inputs["purchase_date"])){
            $inputs["purchase_date"]=Carbon::now()->format("Y-m-d");
        }
        $rollTransit = new RollTransit($inputs->all());
        $rollTransit->save();
        return $rollTransit->id;
    }

    public function resizeRollFromClient($id){
        return self::where("id",$id)->update([
           "client_detail_id" => null,
           "estimate_delivery_date" => null,
           "bag_type_id" => null,
           "bag_unit" => null,
           "w" => null,
           "l" => null,
           "g" => null,
           "printing_color" => null,
           "loop_color" => null,
        ]);
    }

    public function getRollOrm(){
        return self::where("lock_status",false);
    }
    public function getCuttingSchedule(){
        return $this->hasOne(CuttingScheduleDetail::class,"roll_id","id")->where("lock_status",false);
    }
    public function getPrintingSchedule(){
        return $this->hasOne(PrintingScheduleDetail::class,"roll_id","id")->where("lock_status",false);
    }

    public function getAcceptedGarbage(){
        return $this->hasMany(GarbageAcceptRegister::class,"roll_id","id")->where("lock_status",false);
    }

    public function getNotAcceptedGarbage(){
        return $this->hasMany(GarbageNotAcceptRegister::class,"roll_id","id")->where("lock_status",false);
    }

    public function getVendor(){
        return $this->belongsTo(VendorDetailMaster::class,"vender_id","id");
    }

    public function getClient(){
        return $this->belongsTo(ClientDetailMaster::class,"client_detail_id","id");
    }

    public function getBagType(){
        return $this->belongsTo(BagTypeMaster::class,"bag_type_id","id");
    }

    public function getQualityType(){
        return $this->belongsTo(RollQualityMaster::class,"quality_id","id");
    }
    public function getOrderRollBagType(){
        return $this->belongsTo(OrderRollBagType::class,"id",'roll_id')->where("lock_status",false);
    }
    public function getOrder(){
        return $this->hasOneThrough(OrderPunchDetail::class,OrderRollBagType::class,"order_id","id","id","id")->where("order_roll_bag_types.lock_status",false);
    }
}
