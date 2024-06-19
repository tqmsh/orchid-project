<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    // Define the table if it doesn't follow Laravel's naming conventions
    protected $table = 'systems';

    // Define the fillable fields
    protected $fillable = [
        'package_name',
        'price',
        'speaker_1',
        'speaker_2',
        'speaker_3',
        'speaker_4',
        'speaker_5',
        'speaker_6',
        'speaker_7',
        'speaker_8',
        'player_1',
        'player_2',
        'mixer',
        'light_1',
        'light_2',
        'light_3',
        'light_4',
        'light_5',
        'light_6',
        'light_7',
        'light_8',
        'truss_system',
        'cable_box',
    ];

    // Define relationships if needed
    // For example, if a System belongs to a specific Event
    // public function event()
    // {
    //     return $this->belongsTo(Event::class);
    // }
}
