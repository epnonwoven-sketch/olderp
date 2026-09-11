<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class RollColorMaster extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'color',
        "lock_status",
    ];

    public function store($request){  
        $test = self::where("color",$request->color)
                ->first();
        if($test){
            $newRequest = new Request(["id"=>$test->id,"lockStatus"=>false]);
            $this->edit($newRequest);
            return $test->id;
        }      
        $inputs = snakeCase($request);
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

    public function getRollColorListOrm(){
        return self::where("lock_status",false);
    }
}
