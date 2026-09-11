<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTypeMaster extends Model
{
    //
    use Loggable;
    use HasFactory; 

    protected $fillable =[
        "user_type",
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
    
    public function getMenuList(){
        return $this->hasMany(MenuPermission::class,"user_type_master_id","id")->where("lock_status",false)->get();
    }
}
