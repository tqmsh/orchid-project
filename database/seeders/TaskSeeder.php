<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tasks')->insert([
            [
                'name' => 'Fanta',
                'cost' => 4,
                'cnt' => 15,
                'descr' => 'Now with orange fruit juice',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Coca Cola',
                'cost' => 5,
                'cnt' => 10,
                'descr' => 'Together Tastes Better',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Lays potato chips',
                'cost' => 4,
                'cnt' => 10,
                'descr' => 'Betcha can\'t eat just one',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
