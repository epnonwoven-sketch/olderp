<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Container\Attributes\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MenuPermission extends Model
{
    use Loggable;
    //
    protected $fillable = ["menu_master_id","user_type_master_id","lock_status"];

    public function store($request){
        $test = self::where("menu_master_id",$request->menu_mstr_id)
                ->where("user_type_master_id",$request->user_type_mstr_id)
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
}
