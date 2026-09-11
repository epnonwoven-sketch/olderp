<?php

namespace App\Observers;

use App\Models\LoopDetail;
use App\Models\RollDetail;
use App\Models\RollTransit;
use App\Traits\Formula;
use Carbon\Carbon;

class RollTransitObserver
{
    use Formula;
    /**
     * Handle the RollTransit "created" event.
     */
    public function created(RollTransit $rollTransit): void
    {
        if(!$rollTransit->roll_no){
            $purchaseDate = Carbon::parse($rollTransit->purchase_date);
            $rolNo = $purchaseDate->clone()->format("m")."/".$purchaseDate->clone()->format("y")."-";
            $sl = RollTransit::where("purchase_date",$rollTransit->purchase_date)->where("size",">",2)->count("id");
            $sl2 = RollDetail::where("purchase_date",$rollTransit->purchase_date)->where("size",">",2)->count("id");
            $sl3 = LoopDetail::where("purchase_date",$rollTransit->purchase_date)->where("size",">",2)->count("id");
            $sl = $sl+$sl2+$sl3;
            $slNo="";
            while(true){   
                $slNo = str_pad((string)$sl,4,"0",STR_PAD_LEFT);
                $sl=($sl+1);             
                $test = RollTransit::where("roll_no",$rolNo.$slNo)->where("size",">",2)->count();
                $test2 = RollDetail::where("roll_no",$rolNo.$slNo)->where("size",">",2)->count();
                $test3 = LoopDetail::where("roll_no",$rolNo.$slNo)->where("size",">",2)->count();
                if((!$test) && (!$test2) &&(!$test3)){                    
                    $rolNo.=$slNo;
                    break;
                }
            }
            if($rollTransit->size>2)
            $rollTransit->roll_no  = $rolNo;
        }
        if(!$rollTransit->gsm_variation){
            $this->gsmVariation($rollTransit);
            // $rollTransit->gsm_variation = (((($rollTransit->net_weight * 39.37 * 1000) / $rollTransit->size)/$rollTransit->length)-$rollTransit->gsm)/$rollTransit->gsm;
        }
        $rollTransit->saveQuietly();
    }

    /**
     * Handle the RollTransit "updated" event.
     */
    public function updated(RollTransit $rollTransit): void
    {
        //
        if ($rollTransit->isDirty('gsm')){
            $this->gsmVariation($rollTransit);
        } 
        // foreach ($rollTransit->getDirty() as $attribute => $newValue) {
        //     $oldValue = $rollTransit->getOriginal($attribute);
        //     dd($oldValue,$rollTransit->getDirty());
        // }
                   
        $rollTransit->saveQuietly();

    }

    /**
     * Handle the RollTransit "deleted" event.
     */
    public function deleted(RollTransit $rollTransit): void
    {
        //
    }

    /**
     * Handle the RollTransit "restored" event.
     */
    public function restored(RollTransit $rollTransit): void
    {
        //
    }

    /**
     * Handle the RollTransit "force deleted" event.
     */
    public function forceDeleted(RollTransit $rollTransit): void
    {
        //
    }
}
