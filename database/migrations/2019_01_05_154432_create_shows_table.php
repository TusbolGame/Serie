<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShowsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('shows', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->uuid('uuid');
            $table->text('alternative_name')->nullable();
            $table->integer('api_id')->unsigned()->unique();
            $table->text('api_link');
            $table->smallInteger('api_rating')->unsigned()->nullable();
            $table->text('imdb_link')->nullable();
            $table->decimal('imdb_vote', 3, 1)->nullable();
            $table->text('description')->nullable();
            $table->string('language', 30)->nullable();
            $table->integer('network_id')->unsigned()->nullable()->index();
            $table->smallInteger('running_time')->unsigned()->nullable();
            $table->time('airing_time')->nullable();
            $table->integer('content_rating_id')->unsigned()->nullable()->index();
            $table->integer('status_id')->unsigned()->nullable()->index();
            $table->string('timezone', 50)->nullable();
            $table->string('banner', 36)->nullable();
            $table->integer('poster_id')->unsigned()->nullable()->index();
            $table->timestamps();

            $table->foreign('network_id')->references('id')->on('networks')
                ->onUpdate('cascade');
            $table->foreign('content_rating_id')->references('id')->on('content_ratings')
                ->onUpdate('cascade');
            $table->foreign('status_id')->references('id')->on('statuses')
                ->onUpdate('cascade');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('shows');
    }
}
