<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RateTypeMaster extends Model
{
    use Loggable;
    use HasFactory;
    protected $fillable = [
        "rate_type",
        "user_id",
        'lock_status',
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $inputs["user_id"] = $inputs["user_id"]??false ? $inputs["user_id"] : Auth()->user()->id??null;
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

    public function getRateTypeListOrm(){
        return self::where("lock_status",false);
    }
}
