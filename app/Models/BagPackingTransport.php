<?php

namespace App\Models;

use App\Traits\Loggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagPackingTransport  extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'vehicle_no',
        "transporter_name",
        "transporter_id",
        "auto_id",
        "transport_date",
        'bill_no',
        'invoice_no',
        "transport_status",
        "transport_init_status",
        "godown_type_id",
        'user_id',
        "reiving_user_id",
        'lock_status',
        "chalan_unique_id",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        if(!isset($inputs["transport_date"])){
            $inputs["transport_date"]=Carbon::now()->format("Y-m-d");
        }
        $id= self::create($inputs->all())->id;
        return $id;
    }

    public function getBag(){
        return $this->hasManyThrough(BagPacking::class,BagPackingTransportDetail::class,"pack_transport_id","id","id","bag_packing_id","id");
    }
}
