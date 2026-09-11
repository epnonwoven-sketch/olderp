<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintingRegister extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable =[
        "printing_id",
        "roll_id",
        "printing_color_json",
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
        $data = self::find($request->id);
        if($data){
            $data->fill($inputs->all());
            $data->update();
            return true;
        }
        return false;
    }
}
