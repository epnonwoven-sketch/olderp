<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarbageNotAcceptRegister extends Model
{
    use Loggable;
    use HasFactory;
    protected $fillable =[
        "roll_id",
        "total_qtr",
        "user_id",
        "operator_id",
        "helper_id",        
        "shift",
        "lock_status",
    ];

    public function store($request){                
        $inputs = snakeCase($request);
        if(!isset($inputs["user_id"])){
            $inputs["user_id"] = Auth()->user()->id??null;
        }
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
