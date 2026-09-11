<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingOrderBagType extends OrderRollBagType
{
    use Loggable;
    use HasFactory;
    protected $fillable = [
        'order_id',
        "bag_type_id",
        "bag_unit",
        "loop_color",
        "w",
        "l",
        "g",
        "printing_color",
        "lock_status",
    ];
}
