<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'name',
        'description',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function customers()
    {
        return $this->belongsToMany(User::class, 'customer_group_user')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(CustomerChatMessage::class);
    }
}
