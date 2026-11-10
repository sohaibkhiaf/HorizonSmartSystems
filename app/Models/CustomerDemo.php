<?php

namespace App\Models;

use Database\Factories\CustomerDemoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDemo extends Model
{
    /** @use HasFactory<CustomerDemoFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'public_software_id',
        'name',
        'demo_link',
        'admin_note',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function publicSoftware(): BelongsTo
    {
        return $this->belongsTo(PublicSoftware::class);
    }
}
