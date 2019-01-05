<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiUpdatesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('api_updates', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('show_id');
            $table->integer('update_id');
            $table->dateTime('api_update_at');
            $table->timestamps();
        });

        Schema::create('api_update_show', function (Blueprint $table) {
            $table->integer('show_id');
            $table->integer('api_update_id');
            $table->primary(['api_update_id', 'show_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('api_updates');
    }
}
