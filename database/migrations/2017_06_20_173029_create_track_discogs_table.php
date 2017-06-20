<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackDiscogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track_discogs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('track_id')->index()->unsigned();
            $table->integer('discog_id')->index()->unsigned();
            $table->string('state');
            $table->timestamps();
        });

        Schema::table('track_discogs', function (Blueprint $table) {
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('track_discogs');
    }
}
