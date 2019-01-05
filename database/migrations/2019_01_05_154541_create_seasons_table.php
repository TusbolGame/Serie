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
            $table->integer('show_id')->unsigned()->nullable()->index();
            $table->integer('season')->index();
            $table->integer('episodes')->nullable();
            $table->date('date_start');
            $table->date('date_end');
            $table->timestamps();

            $table->foreign('show_id')->references('id')->on('shows')
                ->onUpdate('cascade');
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
