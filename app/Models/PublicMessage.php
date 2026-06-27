<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicMessage extends Model
{
    /** @use HasFactory<\Database\Factories\PublicMessageFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'full_name',
        'username',
        'phone',
        'subject',
        'message',
        'is_seen',
        'admin_note',
    ];
}
