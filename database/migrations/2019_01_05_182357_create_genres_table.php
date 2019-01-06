<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenresTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('genres', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 40);
            $table->string('image', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('genre_show', function (Blueprint $table) {
            $table->integer('genre_id')->unsigned()->nullable();
            $table->integer('show_id')->unsigned()->nullable();
            $table->primary(['genre_id', 'show_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('genres');
        Schema::dropIfExists('genre_show');
    }
}
