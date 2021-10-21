<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    Use HasFactory;

    /**
     * Get the user that owns the device.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
