<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = [
        'descriptive_term',
        'class_of_resource',
        'brand',
        'model_name_number',
        'date_purchased',
        'purchased_new_or_used',
    ];

    // Additional model logic or relationships can be defined here
}
