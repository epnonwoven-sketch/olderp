<?php

namespace App\Observers;

use App\Models\RollDetail;
use App\Traits\Formula;

class RollDetailObserver
{
    use Formula;
    /**
     * Handle the RollDetail "created" event.
     */
    public function created(RollDetail $rollDetail): void
    {
        //
    }

    /**
     * Handle the RollDetail "updated" event.
     */
    public function updated(RollDetail $rollDetail): void
    {
        //
        if ($rollDetail->isDirty('gsm')){
            $this->gsmVariation($rollDetail);
        } 
        
        $rollDetail->saveQuietly();
    }

    /**
     * Handle the RollDetail "deleted" event.
     */
    public function deleted(RollDetail $rollDetail): void
    {
        //
    }

    /**
     * Handle the RollDetail "restored" event.
     */
    public function restored(RollDetail $rollDetail): void
    {
        //
    }

    /**
     * Handle the RollDetail "force deleted" event.
     */
    public function forceDeleted(RollDetail $rollDetail): void
    {
        //
    }
}
