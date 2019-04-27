<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTorrentsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('torrents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('episode_id')->unsigned()->nullable()->index();
            $table->text('file_name')->nullable();
            $table->string('hash', '40');
            $table->bigInteger('file_size')->nullable();
            $table->boolean('used');
            $table->boolean('deleted');
            $table->integer('status')->unsigned()->nullable()->index()->default(0);
            $table->integer('video_quality_id')->unsigned()->nullable()->index();
            $table->dateTime('started_at')->nullable()->index();
            $table->dateTime('finished_at')->nullable()->index();
            $table->dateTime('converted_at')->nullable()->index();
            $table->timestamps();

            $table->foreign('episode_id')->references('id')->on('episodes')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('video_quality_id')->references('id')->on('video_qualities')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('torrents');
    }
}
