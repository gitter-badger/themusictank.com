<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('track_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('position')->index()->unsigned();
            $table->integer('track_id')->index()->unsigned();
            $table->integer('user_id')->nullable()->unsigned();
            $table->double('avg_groove');
            $table->double('low_avg_groove');
            $table->double('high_avg_groove');
            $table->timestamps();
        });

        Schema::table('track_reviews', function (Blueprint $table) {
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
        Schema::dropIfExists('track_reviews');
    }
}
