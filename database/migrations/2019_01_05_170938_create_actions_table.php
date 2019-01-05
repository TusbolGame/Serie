<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_view_id')->nullable()->unsigned()->index();
            $table->integer('action_type_id')->nullable()->unsigned()->index();
            $table->timestamps();

            $table->foreign('video_view_id')->references('id')->on('video_views')
                ->onUpdate('cascade');
            $table->foreign('action_type_id')->references('id')->on('action_types')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('actions');
    }
}
