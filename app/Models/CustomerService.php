<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerService extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerServiceFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 

        'name', 
        'expires_at', 
        'status',

        'license_key', 
        'download_link', 
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
