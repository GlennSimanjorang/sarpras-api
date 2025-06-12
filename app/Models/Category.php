<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;

    protected $fillable = [
        "slug",
        "name"
    ];

    public function items()
    {
        return $this->belongsToMany(Item::class, "item_categories");
    }
}
