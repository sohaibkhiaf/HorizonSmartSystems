<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreRequest extends Model
{
    /** @use HasFactory<\Database\Factories\StoreRequestFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'store_subscription_id',

        'client_full_name',
        'client_contact',
        'client_email',
        'client_address',

        'quantity', 
        'status',
        
        'admin_note',
    ];

    public function storeSubscription() {
        return $this->belongsTo(StoreSubscription::class);
    }
    
}
