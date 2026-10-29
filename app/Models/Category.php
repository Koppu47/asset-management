<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    /**
     * Relationship: 1 Category has many Assets
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
