<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagPackingTransportDetail  extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'pack_transport_id',
        "bag_packing_id",
        "is_delivered",
        "reiving_user_id",
        "is_bag_return",
        "lock_status",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
