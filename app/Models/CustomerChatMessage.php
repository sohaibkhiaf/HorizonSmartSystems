<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerChatMessage extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerChatMessageFactory> */
    use HasFactory; 
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'sender_type',
        'message',
        'link',
        'image',
        'is_seen',
    ];

    protected $casts = [
        'is_seen' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
