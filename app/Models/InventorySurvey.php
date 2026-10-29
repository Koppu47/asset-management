<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventorySurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'started_by',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventorySurveyItem::class);
    }

    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    public function isComplete(): bool
    {
        return $this->completed_at !== null;
    }
}
