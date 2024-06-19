<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// 教程 screen
use Orchid\Screen\AsSource;

class UserSetting extends Model
{
    use HasFactory, AsSource;

    public function task()
    {
        return $this->belongsTo(Task::class, 'item_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}