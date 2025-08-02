<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Attributes that can be mass-assigned
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Attributes that should be hidden from array or JSON outputs
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Casts attributes to native types
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Optional: You can add your custom validation here, but it's usually done during login
    // For password hashing, it's handled by Laravel Auth.
    
    // Relationship with Employee (One-to-One)
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // Optional: You can add any custom methods here like token creation or additional queries
}
