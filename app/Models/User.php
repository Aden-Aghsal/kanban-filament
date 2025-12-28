<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasAvatar, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* =========================
     |  FILAMENT ACCESS CONTROL
     ========================= */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasRole('admin');
        }

        if ($panel->getId() === 'user') {
            return $this->hasRole('user') || $this->hasRole('admin');
        }

        return false;
    }

    /* =========================
     |  FILAMENT AVATAR
     ========================= */
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar
            ?: 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }

    /* =========================
     |  ROLE CHECKER
     ========================= */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /* =========================
     |  RELATION
     ========================= */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
