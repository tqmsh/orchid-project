<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('systems', function (Blueprint $table) {
            $table->id();
            $table->string('package_name');
            $table->integer('price');
            $table->string('speaker_1')->nullable();
            $table->string('speaker_2')->nullable();
            $table->string('speaker_3')->nullable();
            $table->string('speaker_4')->nullable();
            $table->string('speaker_5')->nullable();
            $table->string('speaker_6')->nullable();
            $table->string('speaker_7')->nullable();
            $table->string('speaker_8')->nullable();
            $table->string('player_1')->nullable();
            $table->string('player_2')->nullable();
            $table->string('mixer')->nullable();
            $table->string('light_1')->nullable();
            $table->string('light_2')->nullable();
            $table->string('light_3')->nullable();
            $table->string('light_4')->nullable();
            $table->string('light_5')->nullable();
            $table->string('light_6')->nullable();
            $table->string('light_7')->nullable();
            $table->string('light_8')->nullable();
            $table->string('truss_system')->nullable();
            $table->string('cable_box')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('systems');
    }
}
