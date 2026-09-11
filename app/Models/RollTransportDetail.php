<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollTransportDetail extends Model
{
    use Loggable;
    use HasFactory;
    protected $fillable = [
        'roll_transport_id',
        "roll_id",
        "is_delivered",
        "reiving_user_id",
        "reiving_date",
        "is_roll_return",
        "lock_status",
    ];

    public function store($request){        
        $inputs = snakeCase($request);        
        $rollTransport = new RollTransportDetail($inputs->all());
        $rollTransport->save();
        return $rollTransport->id;
    }
}
