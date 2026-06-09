<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = ['nom', 'prenom', 'email', 'password', 'role', 'photo'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return ['role' => $this->role];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isVisiteur(): bool
    {
        return $this->role === 'visiteur';
    }

    public function canAnalyse(): bool
    {
        return in_array($this->role, ['admin', 'user']);
    }

    public function zones()
    {
        return $this->hasMany(ZoneForestiere::class);
    }

    public function analyses()
    {
        return $this->hasMany(Analyse::class);
    }

    public function rapports()
    {
        return $this->hasMany(Rapport::class);
    }
}
