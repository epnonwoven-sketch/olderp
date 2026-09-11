<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelLog extends Model
{
    use HasFactory;
    protected $fillable = ['model_type', 'model_id', 'action', 'changes', 'route_name','payload',"user_id", 'url'];

}
