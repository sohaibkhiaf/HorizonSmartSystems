<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    /** @use HasFactory<\Database\Factories\RequestFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'operator_id',
        'client_name',
        'client_contact',
        'client_email',
        'client_address',
        'quantity', 
        'status',
        'admin_note',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
    
}
