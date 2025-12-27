<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\HasAvatar;
// --- Tambahkan Import Ini ---
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

// --- Implementasikan FilamentUser ---
class User extends Authenticatable implements HasAvatar, FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'google_id',
        'role',
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
        // Jika mencoba akses panel Admin
        if ($panel->getId() === 'admin') {
            return $this->role === 'admin';
        }

        // Jika mencoba akses panel User (id-nya adalah 'user')
        if ($panel->getId() === 'user') {
            return true; // Semua user yang login (termasuk admin) boleh masuk panel User
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
        return $this->role === 'admin';
    }

    /* =========================
     |  RELATION
     ========================= */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}