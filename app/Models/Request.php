<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    /** @use HasFactory<\Database\Factories\RequestFactory> */
    use HasFactory;

    protected $fillable = [
        'full_name',
        'company_name',
        'phone',
        'email',
        'service_type',
        'project_title',
        'project_description',
        'visual_identity',
    ];
}
