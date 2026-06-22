<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerChatMessage extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerChatMessageFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'sender_type',
        'sender_id',
        'receiver_id',
        'customer_group_id',
        'sender_name',
        'receiver_name',
        'message',
        'is_seen',
        'link',
        'image',
    ];

    protected $casts = [
        'is_seen' => 'boolean',
    ];

    // Customer conversation owner
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    public function getSenderDisplayNameAttribute(): string
    {
        return $this->sender_name
            ?: ($this->sender?->name ?: ($this->sender_type === 'customer' ? 'Client' : 'Admin'));
    }

    public function getReceiverDisplayNameAttribute(): string
    {
        if ($this->customer_group_id && $this->customerGroup) {
            return 'Groupe : ' . $this->customerGroup->name;
        }

        return $this->receiver_name
            ?: ($this->receiver?->name ?: 'Tous les admins / opérateurs');
    }
}
