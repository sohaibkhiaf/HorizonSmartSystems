<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\CustomerChatMessage;

class CustomerChatMessageObserver
{
    /**
     * Handle the CustomerChatMessage "created" event.
     */
    public function created(CustomerChatMessage $customerChatMessage): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'model_type' => CustomerChatMessage::class,
            'model_id'   => $customerChatMessage->id,
        ]);
    }

    /**
     * Handle the CustomerChatMessage "updated" event.
     */
    public function updated(CustomerChatMessage $customerChatMessage): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'updated',
            'model_type' => CustomerChatMessage::class,
            'model_id'   => $customerChatMessage->id,
        ]);
    }

    /**
     * Handle the CustomerChatMessage "deleted" event.
     */
    public function deleted(CustomerChatMessage $customerChatMessage): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'deleted',
            'model_type' => CustomerChatMessage::class,
            'model_id'   => $customerChatMessage->id,
        ]);
    }

    /**
     * Handle the CustomerChatMessage "restored" event.
     */
    public function restored(CustomerChatMessage $customerChatMessage): void
    {
        //
    }

    /**
     * Handle the CustomerChatMessage "force deleted" event.
     */
    public function forceDeleted(CustomerChatMessage $customerChatMessage): void
    {
        //
    }
}
