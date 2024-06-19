<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'address',
        'city',
        'state_province',
        'zip_postal',
        'country',
        'cell_phone',
        'email',
        'staff_type',
    ];
}
