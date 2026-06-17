<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\StoreSubscription;

class StoreSubscriptionObserver
{
    /**
     * Handle the StoreSubscription "created" event.
     */
    public function created(StoreSubscription $storeSubscription): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'model_type' => StoreSubscription::class,
            'model_id'   => $storeSubscription->id,
        ]);
    }

    /**
     * Handle the StoreSubscription "updated" event.
     */
    public function updated(StoreSubscription $storeSubscription): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'updated',
            'model_type' => StoreSubscription::class,
            'model_id'   => $storeSubscription->id,
        ]);
    }

    /**
     * Handle the StoreSubscription "deleted" event.
     */
    public function deleted(StoreSubscription $storeSubscription): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'deleted',
            'model_type' => StoreSubscription::class,
            'model_id'   => $storeSubscription->id,
        ]);
    }

    /**
     * Handle the StoreSubscription "restored" event.
     */
    public function restored(StoreSubscription $storeSubscription): void
    {
        //
    }

    /**
     * Handle the StoreSubscription "force deleted" event.
     */
    public function forceDeleted(StoreSubscription $storeSubscription): void
    {
        //
    }
}
