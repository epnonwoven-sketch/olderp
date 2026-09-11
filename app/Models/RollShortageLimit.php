<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollShortageLimit extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = [
        "roll_color",
        "roll_size",
        "roll_gsm",
        "quality_type_id",
        "min_limit",
        "user_id",
        'lock_status',
    ];


    public function store($request){        
        $inputs = snakeCase($request);
        $test= self::where("roll_color",$inputs["roll_color"])
                ->where("roll_size",$inputs["roll_size"])
                ->where("roll_gsm",$inputs["roll_gsm"])
                ->where("quality_type_id",$inputs["quality_type_id"])
                ->first();
        if($test){
            $request->merge(["id"=>$test->id,"lock_status"=>false]);
            $this->edit($request);
            return $test->id;
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
}
