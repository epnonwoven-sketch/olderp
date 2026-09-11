<?php

namespace App\Observers;

use App\Models\OrderPunchDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderPunchDetailObserver
{
    /**
     * Handle the OrderPunchDetail "created" event.
     */
    public function created(OrderPunchDetail $orderPunchDetail): void
    {
        if(!$orderPunchDetail->order_no){
            $orderDate = Carbon::parse(Carbon::now());
            $rolNo = 'ODR/'.$orderDate->clone()->format("dmy")."-";
            $sl = OrderPunchDetail::where(DB::raw("CAST(created_at AS DATE)"),$orderDate->clone()->format('Y-m-d'))->count("id");
            $slNo ="";
            while(true){   
                $slNo = str_pad((string)$sl,4,"0",STR_PAD_LEFT);
                $sl=($sl+1);             
                $test = OrderPunchDetail::where("order_no",$rolNo.$slNo)->count();
                if((!$test)){                    
                    $rolNo.=$slNo;
                    break;
                }
            }
            $orderPunchDetail->order_no  = $rolNo;
        }
        $orderPunchDetail->saveQuietly();
    }

    /**
     * Handle the OrderPunchDetail "updated" event.
     */
    public function updated(OrderPunchDetail $orderPunchDetail): void
    {
        //
    }

    /**
     * Handle the OrderPunchDetail "deleted" event.
     */
    public function deleted(OrderPunchDetail $orderPunchDetail): void
    {
        //
    }

    /**
     * Handle the OrderPunchDetail "restored" event.
     */
    public function restored(OrderPunchDetail $orderPunchDetail): void
    {
        //
    }

    /**
     * Handle the OrderPunchDetail "force deleted" event.
     */
    public function forceDeleted(OrderPunchDetail $orderPunchDetail): void
    {
        //
    }
}
