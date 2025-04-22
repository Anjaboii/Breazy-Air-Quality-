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

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->where('slug', $role)->count();
        }
        
        return !!$role->intersect($this->roles)->count();
    }
    
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
    
    public function isMonitoringAdmin()
    {
        return $this->hasRole('monitoring-admin');
    }
}