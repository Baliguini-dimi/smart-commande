<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ─── Vérifications de rôle ───────────────────
    public function isGerant(): bool
    {
        return $this->role === 'gerant';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    // ─── Relations ───────────────────────────────
    public function restaurant()
    {
        return $this->hasOne(Restaurant::class);
    }
}