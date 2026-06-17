<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\PublicSoftware;

class PublicSoftwareObserver
{
    /**
     * Handle the PublicSoftware "created" event.
     */
    public function created(PublicSoftware $publicSoftware): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'model_type' => PublicSoftware::class,
            'model_id'   => $publicSoftware->id,
        ]);
    }

    /**
     * Handle the PublicSoftware "updated" event.
     */
    public function updated(PublicSoftware $publicSoftware): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'updated',
            'model_type' => PublicSoftware::class,
            'model_id'   => $publicSoftware->id,
        ]);
    }

    /**
     * Handle the PublicSoftware "deleted" event.
     */
    public function deleted(PublicSoftware $publicSoftware): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'deleted',
            'model_type' => PublicSoftware::class,
            'model_id'   => $publicSoftware->id,
        ]);
    }

    /**
     * Handle the PublicSoftware "restored" event.
     */
    public function restored(PublicSoftware $publicSoftware): void
    {
        //
    }

    /**
     * Handle the PublicSoftware "force deleted" event.
     */
    public function forceDeleted(PublicSoftware $publicSoftware): void
    {
        //
    }
}
