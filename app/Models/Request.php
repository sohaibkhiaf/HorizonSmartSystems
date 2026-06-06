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
        'status',
        'delivery_credentials',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function notes() {
        return $this->hasMany(Note::class);
    }
    
}
