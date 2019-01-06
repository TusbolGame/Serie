<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('posters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('posterable_id')->unsigned()->nullable()->index();
            $table->string('posterable_type', 50)->index();
            $table->string('name', 36);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()  {
        Schema::dropIfExists('posters');
    }
}
