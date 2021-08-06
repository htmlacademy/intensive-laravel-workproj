<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowUserRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('show_user_rating', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Show::class);
            $table->foreignIdFor(\App\Models\User::class);
            $table->integer('rating');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('show_user_rating');
    }
}
