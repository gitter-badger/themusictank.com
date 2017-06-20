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
            $table->integer('artist_id')->index()->unsigned();
            $table->integer('album_id')->index()->unsigned();
            $table->string('youtube_key')->nullable();
            $table->integer('length')->nullable()->unsigned();
            $table->integer('position_int')->nullable()->unsigned();
            $table->string('position')->nullable();
            $table->timestamps();
        });

        Schema::table('tracks', function (Blueprint $table) {
            $table->foreign('artist_id')->references('id')->on('artists')->onDelete('cascade');
            $table->foreign('album_id')->references('id')->on('albums')->onDelete('cascade');
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
