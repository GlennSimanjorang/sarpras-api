<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowingFactory> */
    use HasFactory;

    protected $fillable = [
        "item_id",
        "user_id",
        "quantity",
        "status",
        "approved_by",
        "approved_at",
        "due_date",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(Admin::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function returning()
    {
        return $this->hasOne(Returning::class);
    }
}
