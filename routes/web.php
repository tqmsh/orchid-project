<?php

use Illuminate\Support\Facades\Route;
use App\Orchid\Screens\TaskScreen;
Route::post('/restock/{task}', 'TaskController@restock')->name('task.restock');

Route::get('/', function () {
    return view('welcome');
});
