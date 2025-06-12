<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    protected $fillable = [
        "sku",
        "name",
        "image_url",
        "stock"
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, "item_categories");
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
}
