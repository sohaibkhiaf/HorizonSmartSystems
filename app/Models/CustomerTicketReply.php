<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerTicketReply extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerTicketReplyFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'customer_ticket_id', 
        'user_id', 
        'author_type', 
        'message',
    ];

    public function customerTicket() {
        return $this->belongsTo(CustomerTicket::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
