<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlbumDiscogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('album_discogs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('album_id')->index()->unsigned();
            $table->integer('discog_id')->index()->unsigned();
            $table->string('state');
            $table->timestamps();
        });

        Schema::table('album_discogs', function (Blueprint $table) {
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
        Schema::dropIfExists('album_discogs');
    }
}
