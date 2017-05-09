<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackUpvotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track_upvotes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('track_id')->index();
            $table->integer('user_id')->index();
            $table->integer('vote')->unsigned();
            $table->timestamps();

            $table->foreign('track_id')->references('id')->on('tracks');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('track_upvotes');
    }
}
