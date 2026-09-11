<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRollBagType extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'order_id',
        "roll_id",
        "lock_status",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }

    public function getOrder()
    {
        return $this->belongsTo(OrderPunchDetail::class, 'order_id', 'id');
    }

}
