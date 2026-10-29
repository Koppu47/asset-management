<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }

    public function inventorySurveysStarted()
    {
        return $this->hasMany(InventorySurvey::class, 'started_by');
    }

    public function inventorySurveyChecks()
    {
        return $this->hasMany(InventorySurveyItem::class, 'checked_by');
    }
}
