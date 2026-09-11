<?php

namespace App\Models;

use App\Traits\Loggable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintingScheduleDetail extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'printing_date',
        "machine_id",
        "roll_id",
        "sl",
        "lock_status",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        if(!isset($inputs["printing_date"])){
            $inputs["printing_date"]=Carbon::now()->format("Y-m-d");
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

    public function getPrintingScheduleOrm(){
        return self::where("lock_status",false);
    }
}
