<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'asset_code',
        'name',
        'serial_number',
        'price',
        'purchase_date',
        'status',
        'image',
        'description',
        'department',
        'building',
        'room',
        'custodian_user_id',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'price' => 'decimal:2',
        ];
    }

    /**
     * Relationship: Asset belongs to Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship: Asset has many BorrowRecords
     */
    public function borrowRecords()
    {
        return $this->hasMany(BorrowRecord::class);
    }

    public function inventorySurveyItems()
    {
        return $this->hasMany(InventorySurveyItem::class);
    }

    public function custodian()
    {
        return $this->belongsTo(User::class, 'custodian_user_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AssetAuditLog::class)->latest();
    }

    /**
     * Check if asset is available for borrowing
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
