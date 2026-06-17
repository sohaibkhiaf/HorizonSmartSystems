<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\CustomerDemo;

class CustomerDemoObserver
{
    /**
     * Handle the CustomerDemo "created" event.
     */
    public function created(CustomerDemo $customerDemo): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'model_type' => CustomerDemo::class,
            'model_id'   => $customerDemo->id,
        ]);
    }

    /**
     * Handle the CustomerDemo "updated" event.
     */
    public function updated(CustomerDemo $customerDemo): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'updated',
            'model_type' => CustomerDemo::class,
            'model_id'   => $customerDemo->id,
        ]);
    }

    /**
     * Handle the CustomerDemo "deleted" event.
     */
    public function deleted(CustomerDemo $customerDemo): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'deleted',
            'model_type' => CustomerDemo::class,
            'model_id'   => $customerDemo->id,
        ]);
    }

    /**
     * Handle the CustomerDemo "restored" event.
     */
    public function restored(CustomerDemo $customerDemo): void
    {
        //
    }

    /**
     * Handle the CustomerDemo "force deleted" event.
     */
    public function forceDeleted(CustomerDemo $customerDemo): void
    {
        //
    }
}
