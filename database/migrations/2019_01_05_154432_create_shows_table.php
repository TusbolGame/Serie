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
            $table->text('name')->unique();
            $table->text('alternative_name')->nullable();
            $table->integer('api_id')->unsigned()->unique();
            $table->text('api_link');
            $table->integer('rating')->unsigned()->nullable();
            $table->text('imdb_link')->nullable();
            $table->decimal('imdb_vote', 3, 1)->nullable();
            $table->text('description');
            $table->string('language', 30)->nullable();
            $table->integer('network_id')->unsigned()->nullable()->index();
            $table->integer('running_time')->nullable();
            $table->integer('content_rating_id')->unsigned()->nullable()->index();
            $table->integer('status_id')->unsigned()->nullable()->index();
            $table->string('timezone', 50)->nullable();
            $table->string('banner', 80);
            $table->string('poster', 80);
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
