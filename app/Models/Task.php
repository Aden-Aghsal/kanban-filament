<?php

namespace App\Models;

use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'deadline',
        'canceled_at',
        'canceled_reason',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'deadline' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

public function team()
{
    return $this->belongsTo(Team::class);
}

// Scope untuk filter task tim / individu
public function scopeTeam($query)
{
    return $query->whereNotNull('team_id');
}

public function scopeIndividual($query)
{
    return $query->whereNull('team_id');
}

}
