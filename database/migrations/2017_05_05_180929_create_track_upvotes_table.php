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
            $table->integer('track_id')->index()->unsigned();
            $table->integer('user_id')->index()->unsigned();
            $table->integer('vote')->unsigned();
            $table->timestamps();
        });

        Schema::table('track_upvotes', function (Blueprint $table) {
            $table->foreign('track_id')->references('id')->on('tracks')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
