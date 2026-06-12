<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'phone',
        'company',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'ADMIN';
    }

    public function isOperator()
    {
        return $this->role === 'OPERATOR';
    }

    public function isClient()
    {
        return $this->role === 'CLIENT';
    }


    public function orders() {
        return $this->hasMany(CustomerOrder::class);
    }

    public function services() {
        return $this->hasMany(CustomerService::class);
    }

    public function subscriptions() {
        return $this->hasMany(CustomerSubscription::class);
    }

    public function tickets() {
        return $this->hasMany(CustomerTicket::class);
    }
}
