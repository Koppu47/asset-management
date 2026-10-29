<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAuditLog extends Model
{
    protected $fillable = ['asset_id', 'user_id', 'action', 'old_values', 'new_values'];

    protected function casts(): array
    {
        return ['old_values' => 'array', 'new_values' => 'array'];
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
