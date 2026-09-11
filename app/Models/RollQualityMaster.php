<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollQualityMaster extends Model
{
    use Loggable;
    use HasFactory;
    protected $fillable = [
        "vendor_id",
        "grade_id",
        "quality",
        "user_id",
        'lock_status',
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $test= self::where("vendor_id",$inputs["vendor_id"])->where("quality",$inputs["quality"])->first();
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

    public function getGrade(){
        return $this->belongsTo(GradeMaster::class,"grade_id","id");
    }
}
