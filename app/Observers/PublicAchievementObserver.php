<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\PublicAchievement;

class PublicAchievementObserver
{
    /**
     * Handle the PublicAchievement "created" event.
     */
    public function created(PublicAchievement $publicAchievement): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'model_type' => PublicAchievement::class,
            'model_id'   => $publicAchievement->id,
        ]);
    }

    /**
     * Handle the PublicAchievement "updated" event.
     */
    public function updated(PublicAchievement $publicAchievement): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'updated',
            'model_type' => PublicAchievement::class,
            'model_id'   => $publicAchievement->id,
        ]);
    }

    /**
     * Handle the PublicAchievement "deleted" event.
     */
    public function deleted(PublicAchievement $publicAchievement): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'deleted',
            'model_type' => PublicAchievement::class,
            'model_id'   => $publicAchievement->id,
        ]);
    }

    /**
     * Handle the PublicAchievement "restored" event.
     */
    public function restored(PublicAchievement $publicAchievement): void
    {
        //
    }

    /**
     * Handle the PublicAchievement "force deleted" event.
     */
    public function forceDeleted(PublicAchievement $publicAchievement): void
    {
        //
    }
}
