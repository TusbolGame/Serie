<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNetworksTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('networks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->index();
            $table->smallInteger('type')->nullable()->index();
            $table->string('country', 50)->nullable();
            $table->string('link', 200)->nullable();
            $table->string('banner', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('networks');
    }
}
