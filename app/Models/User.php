<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
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

    // ✅ AUTO ROLE UNTUK USER BARU
    protected static function booted()
    {
        static::created(function ($user) {
            if (! $user->hasAnyRole(['admin', 'user'])) {
                $user->assignRole('user');
            }
        });
    }

    // ✅ FILAMENT PANEL ACCESS (FINAL)
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->hasRole('admin'),
            'user'  => $this->hasRole('user'),
            default => false,
        };
    }

    // ✅ AVATAR
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar
            ?: 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function tasks()
{
    return $this->hasMany(\App\Models\Task::class);
}
}
