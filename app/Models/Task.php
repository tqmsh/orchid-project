<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// 教程 screen
use Orchid\Screen\AsSource;

class Task extends Model
{
    use HasFactory, AsSource;
}