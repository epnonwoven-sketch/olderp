<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityStateMap extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'state_id',
        'city_name',
        "lock_status",
    ];

    public function store($request){        
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

    public function getCityOrm(){
        return self::where("lock_status",false);
    }

    public function getState(){
        return $this->belongsTo(StateMaster::class,"state_id","id");
    }
}
