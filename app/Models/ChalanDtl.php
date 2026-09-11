<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChalanDtl extends Model
{
    use HasFactory;

    protected $fillable = [
        'chalan_no',
        "unique_id",
        "chalan_date",
        "chalan_json",
        "user_id",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
