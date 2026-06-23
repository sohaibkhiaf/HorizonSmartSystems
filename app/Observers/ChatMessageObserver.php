<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\ChatMessage;

class ChatMessageObserver
{
    /**
     * Handle the ChatMessage "created" event.
     */
    public function created(ChatMessage $chatMessage): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'created',
            'model_type' => ChatMessage::class,
            'model_id'   => $chatMessage->id,
        ]);
    }

    /**
     * Handle the ChatMessage "updated" event.
     */
    public function updated(ChatMessage $chatMessage): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'updated',
            'model_type' => ChatMessage::class,
            'model_id'   => $chatMessage->id,
        ]);
    }

    /**
     * Handle the ChatMessage "deleted" event.
     */
    public function deleted(ChatMessage $chatMessage): void
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'deleted',
            'model_type' => ChatMessage::class,
            'model_id'   => $chatMessage->id,
        ]);
    }

    /**
     * Handle the ChatMessage "restored" event.
     */
    public function restored(ChatMessage $chatMessage): void
    {
        //
    }

    /**
     * Handle the ChatMessage "force deleted" event.
     */
    public function forceDeleted(ChatMessage $chatMessage): void
    {
        //
    }
}
