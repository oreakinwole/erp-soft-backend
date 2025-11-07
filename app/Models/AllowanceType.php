<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AllowanceType extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];
}