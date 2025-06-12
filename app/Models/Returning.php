<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returning extends Model
{
    /** @use HasFactory<\Database\Factories\ReturningFactory> */
    use HasFactory;

    protected $fillable = [
        "borrow_id",
        "returned_quantity",
        "handled_by"
    ];

    public function borrowing () {
        return $this->belongsTo(Borrowing::class, "borrow_id");
    }

    public function admin ()
    {
        return $this->belongsTo(Admin::class, "handled_by");
    }
}
