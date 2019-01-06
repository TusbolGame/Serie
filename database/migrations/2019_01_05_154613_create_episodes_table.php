<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEpisodesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('episodes', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid('uuid');
            $table->integer('show_id')->unsigned()->nullable()->index();
            $table->integer('season_id')->unsigned()->nullable()->index();
            $table->integer('episode_number')->nullable()->index();
            $table->string('episode_code', 20);
            $table->dateTime('airing_at')->nullable()->index();
            $table->text('title')->nullable();
            $table->integer('api_id')->unsigned();
            $table->text('api_link');
            $table->text('summary')->nullable();
            $table->integer('poster_id')->unsigned()->nullable()->index();
            $table->timestamps();

            $table->foreign('show_id')->references('id')->on('shows')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('season_id')->references('id')->on('seasons')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('episodes');
    }
}
