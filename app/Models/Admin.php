<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'username',
        'password',
        "last_login_at"
    ];

    protected $hidden = [
        "password"
    ];

    public function approved()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function handled()
    {
        return $this->hasMany(Returning::class);
    }
}
