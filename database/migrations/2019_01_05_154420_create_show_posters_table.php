<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShowPostersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('show_posters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('show_id')->unsigned()->nullable()->index();
            $table->string('name', 60);
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
    public function down()  {
        Schema::dropIfExists('show_posters');
    }
}