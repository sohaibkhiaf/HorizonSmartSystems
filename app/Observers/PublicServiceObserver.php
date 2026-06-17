<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\PublicService;

class PublicServiceObserver
{
    /**
     * Handle the PublicService "created" event.
     */
    public function created(PublicService $publicService): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'model_type' => PublicService::class,
            'model_id'   => $publicService->id,
        ]);
    }

    /**
     * Handle the PublicService "updated" event.
     */
    public function updated(PublicService $publicService): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'updated',
            'model_type' => PublicService::class,
            'model_id'   => $publicService->id,
        ]);
    }

    /**
     * Handle the PublicService "deleted" event.
     */
    public function deleted(PublicService $publicService): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'deleted',
            'model_type' => PublicService::class,
            'model_id'   => $publicService->id,
        ]);
    }

    /**
     * Handle the PublicService "restored" event.
     */
    public function restored(PublicService $publicService): void
    {
        //
    }

    /**
     * Handle the PublicService "force deleted" event.
     */
    public function forceDeleted(PublicService $publicService): void
    {
        //
    }
}
