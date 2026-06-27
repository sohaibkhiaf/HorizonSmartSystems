<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
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
        'voice_message',
        'image',
    ];

    protected $casts = [
        'is_seen' => 'boolean',
    ];

    // conversation owner
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
        if ($this->sender) {
            return $this->sender->name;
        }

        if ($this->sender_name) {
            return $this->sender_name;
        }

        if ($this->sender_type === 'customer' && $this->user) {
            return $this->user->name;
        }

        return $this->sender_type === 'customer' ? 'Client' : 'Admin / Opérateur';
    }

    public function getReceiverDisplayNameAttribute(): string
    {
        if ($this->receiver) {
            return $this->receiver->name;
        }

        if ($this->receiver_name) {
            return $this->receiver_name;
        }

        if ($this->receiver_id === null && in_array($this->sender_type, ['customer', 'admin'], true)) {
            return 'Tous les admins / opérateurs';
        }

        if ($this->customerGroup) {
            return 'Groupe: ' . $this->customerGroup->name;
        }

        return 'Inconnu';
    }
}
