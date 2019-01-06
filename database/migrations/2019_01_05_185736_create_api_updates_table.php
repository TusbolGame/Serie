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
            $table->integer('show_id')->unsigned()->nullable()->index();
            $table->integer('data_update_id')->unsigned()->nullable()->index();
            $table->dateTime('api_updated_at');
            $table->timestamps();

            $table->foreign('show_id')->references('id')->on('shows')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('data_update_id')->references('id')->on('data_updates')
                ->onUpdate('cascade');
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
