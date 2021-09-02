<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShowUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('show_user', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Show::class)->constrained();
            $table->foreignIdFor(\App\Models\User::class)->constrained();
            $table->integer('vote')->nullable();
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
        Schema::dropIfExists('show_user');
    }
}
