<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    /** @use HasFactory<\Database\Factories\NoteFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "content",
        "sender",
        "request_id",
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}
