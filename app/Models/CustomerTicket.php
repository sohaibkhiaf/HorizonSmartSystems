<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTicket extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerTicketFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 
        'subject', 
        'message', 
        'status',
    ];

    public function replies() {
        return $this->hasMany(CustomerTicketReply::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
