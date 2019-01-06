<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeasonsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('seasons', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->integer('show_id')->unsigned()->nullable()->index();
            $table->integer('api_id')->unsigned()->nullable()->index();
            $table->integer('season')->index();
            $table->integer('episodes')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->integer('poster_id')->unsigned()->nullable()->index();
            $table->timestamps();

            $table->foreign('show_id')->references('id')->on('shows')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('seasons');
    }
}
