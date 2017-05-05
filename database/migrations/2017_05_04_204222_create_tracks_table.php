<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->string('slug')->index()->unique();
            $table->string('gid')->index()->unique();
            $table->integer('artist_id')->index();
            $table->integer('album_id')->index();
            $table->string('youtube_key')->nullable();
            $table->integer('length')->nullable();
            $table->integer('position')->nullable();
            $table->timestamps();

            $table->foreign('artist_id')->references('id')->on('artists');
            $table->foreign('album_id')->references('id')->on('albums');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tracks');
    }
}
