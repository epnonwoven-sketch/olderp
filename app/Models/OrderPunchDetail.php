<?php

namespace App\Models;

use App\Traits\Loggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderPunchDetail extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        "order_no",
        "order_date",
        "broker_id",
        'client_detail_id',
        "estimate_delivery_date",
        "delivery_date",
        "is_delivered",
        "payment_mode_id",
        "bag_type_id",
        "bag_quality",
        "bag_gsm",
        "bag_gsm_json",
        "units",
        "total_units",
        "rate_per_unit",
        "bag_w",
        "bag_l",
        "bag_g",
        "bag_loop_color",
        "bag_color",
        "bag_printing_color",
        "booked_units",
        "disbursed_units",
        "grade_id",
        "rate_type_id",
        "fare_type_id",
        "stereo_type_id",
        "lock_status",
        "user_id",
        "disbursed_by",
        "deceived_by",
        "alt_bag_color",
        "alt_bag_gsm",
        "is_draft",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        if(!isset($inputs["order_date"])){
            $inputs["order_date"]=Carbon::now()->format("Y-m-d");
        }
        if(!isset($inputs["user_id"])){
            $inputs["user_id"]=Auth()->user()->id;
        }
        $id= self::create($inputs->all())->id;
        return $id;
    }

    public function edit($request){
        $inputs = snakeCase($request)->filter(function($val,$index){
            return (in_array($index,$this->fillable));
        });
        $model = self::find($request->id);
        if($model){
            $model->fill($inputs->all());
            $model->update();
            return true;
        }
        return false;
    }

    public function getPendingOrderOrm(){
        return self::where("lock_status",false)
                ->where("is_delivered",false)
                ->where(DB::raw("(total_units - disbursed_units)"),">",DB::raw("booked_units"));
    }

    public function getBagType(){
        return $this->belongsTo(BagTypeMaster::class,"bag_type_id","id")->first();
    }

    public function getClient(){
        return $this->belongsTo(ClientDetailMaster::class,"client_detail_id","id")->first();
    }

    public function getGrade(){
        return $this->belongsTo(GradeMaster::class,"grade_id","id")->first();
    }

    public function getRateType(){
        return $this->belongsTo(RateTypeMaster::class,"rate_type_id","id")->first();
    }

    public function getFare(){
        return $this->belongsTo(FareDetail::class,"fare_type_id","id")->first();
    }

    public function getStereo(){
        return $this->belongsTo(StereoDetail::class,"stereo_type_id","id")->first();
    }

    public function getRollTransit(){
        return $this->hasManyThrough(RollTransit::class,OrderRollBagType::class,"order_id","id","id","roll_id","id");
    }

    public function getRollDetail(){
        return $this->hasManyThrough(RollDetail::class,OrderRollBagType::class,"order_id","id","id","roll_id","id");
    }

    public function getOrderRollBagTypes(){
        return $this->hasMany(OrderRollBagType::class,"order_id","id")->where("lock_status",false);
    }
}
