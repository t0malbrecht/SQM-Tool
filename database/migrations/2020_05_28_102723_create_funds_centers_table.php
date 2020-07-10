<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFundsCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funds_centers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('division');
            $table->string('description')->unique();
            $table->bigInteger('fundsCenterNumber')->unique();
            $table->bigInteger('costCenter')->unique();
            $table->string('fond');
            $table->Integer('teachingUnit');
            $table->string('faculty');
            $table->string('professor')->nullable();
            $table->smallInteger('level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funds_centers');
    }
}
