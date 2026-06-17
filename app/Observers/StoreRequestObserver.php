<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\StoreRequest;

class StoreRequestObserver
{
    /**
     * Handle the StoreRequest "created" event.
     */
    public function created(StoreRequest $storeRequest): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'model_type' => StoreRequest::class,
            'model_id'   => $storeRequest->id,
        ]);
    }

    /**
     * Handle the StoreRequest "updated" event.
     */
    public function updated(StoreRequest $storeRequest): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'updated',
            'model_type' => StoreRequest::class,
            'model_id'   => $storeRequest->id,
        ]);
    }

    /**
     * Handle the StoreRequest "deleted" event.
     */
    public function deleted(StoreRequest $storeRequest): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'deleted',
            'model_type' => StoreRequest::class,
            'model_id'   => $storeRequest->id,
        ]);
    }

    /**
     * Handle the StoreRequest "restored" event.
     */
    public function restored(StoreRequest $storeRequest): void
    {
        //
    }

    /**
     * Handle the StoreRequest "force deleted" event.
     */
    public function forceDeleted(StoreRequest $storeRequest): void
    {
        //
    }
}
