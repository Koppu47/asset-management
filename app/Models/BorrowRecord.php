<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'asset_id',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'borrow_date' => 'date',
            'expected_return_date' => 'date',
            'actual_return_date' => 'date',
        ];
    }

    /**
     * Relationship: BorrowRecord belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: BorrowRecord belongs to Asset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
