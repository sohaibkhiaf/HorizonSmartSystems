<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\PublicMessage;

class PublicMessageObserver
{
    /**
     * Handle the PublicMessage "created" event.
     */
    public function created(PublicMessage $publicMessage): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'model_type' => PublicMessage::class,
            'model_id'   => $publicMessage->id,
        ]);
    }

    /**
     * Handle the PublicMessage "updated" event.
     */
    public function updated(PublicMessage $publicMessage): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'updated',
            'model_type' => PublicMessage::class,
            'model_id'   => $publicMessage->id,
        ]);
    }

    /**
     * Handle the PublicMessage "deleted" event.
     */
    public function deleted(PublicMessage $publicMessage): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'deleted',
            'model_type' => PublicMessage::class,
            'model_id'   => $publicMessage->id,
        ]);
    }

    /**
     * Handle the PublicMessage "restored" event.
     */
    public function restored(PublicMessage $publicMessage): void
    {
        //
    }

    /**
     * Handle the PublicMessage "force deleted" event.
     */
    public function forceDeleted(PublicMessage $publicMessage): void
    {
        //
    }
}
