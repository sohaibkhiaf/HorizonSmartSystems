<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerOrder extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerOrderFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'user_id', 
        'name', 
        'price', 
        'status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
