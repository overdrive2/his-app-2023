<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWard extends Model
{
    use HasFactory;

    public function getWardNameAttribute()
    {
        return Ward::find($this->ward_id)->name;
    }
}
