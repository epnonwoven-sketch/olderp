<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollTransport extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'vehicle_no',
        "transporter_name",
        "transport_date",
        "bill_no",
        "invoice_no",
        "transport_status",
        "client_id",
        "vendor_id",
        "purpose",
        "user_id",
        "reiving_user_id",
        "reiving_date",
        "is_fully_reviewed",
        "transporter_id",
        "auto_id",
        "chalan_unique_id",
        "transport_init_status",
        "godown_type_id",
        "lock_status",
    ];

    public function store($request){        
        $inputs = snakeCase($request);        
        $rollTransport = new RollTransport($inputs->all());
        $rollTransport->save();
        return $rollTransport->id;
    }
}
