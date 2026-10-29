<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventorySurveyItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_survey_id',
        'asset_id',
        'status',
        'note',
        'checked_by',
        'checked_at',
        'resolution_status',
        'assigned_to',
        'resolution_note',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(InventorySurvey::class, 'inventory_survey_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
