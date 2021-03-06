<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArtistDiscogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artist_discogs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('artist_id')->index()->unsigned();
            $table->integer('discog_id')->index()->unsigned();
            $table->string('state');
            $table->timestamps();
        });

        Schema::table('artist_discogs', function (Blueprint $table) {
            $table->foreign('artist_id')->references('id')->on('artists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('artist_discogs');
    }
}
