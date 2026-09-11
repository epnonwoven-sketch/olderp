<?php

namespace App\Observers;

use App\Models\ModelLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogObserver
{
    public function created($model)
    {
        $this->logChanges($model, 'created');
    }

    public function updated($model)
    {
        $this->logChanges($model, 'updated');
    }

    public function deleted($model)
    {
        $this->logChanges($model, 'deleted');
    }

    protected function logChanges($model, $action)
    {
        ModelLog::create([
            'model_type' => get_class($model), // Model class
            'model_id' => $model->getKey(), // Primary key of the model
            'action' => $action, // Action type (created, updated, deleted)
            'changes' => json_encode([
                'old' => $action === 'updated' ? $model->getOriginal() : null, // Old values only for updated actions
                'new' => $action !== 'deleted' ? $model->getDirty() : null, // Dirty values only for updated/created actions
            ]), // Old and new values in JSON
            'route_name' => Request::route() ? Request::route()->getName() : null, // Avoid null errors for route
            'payload' => json_encode(request()->all()), // Convert payload to JSON string
            "user_id"=>Auth()->user()->id??null,
            'url' => Request::fullUrl(), // Full URL of the request
        ]);
    }

}

