<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOngoingPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ongoing_payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('claim_id');
            $table->unsignedBigInteger('favoredFundsCenter_id');
            $table->unsignedBigInteger('chargedFundsCenter_id');
            $table->unsignedBigInteger('costType_id');
            $table->smallInteger('grantedFunds');
            $table->date('plannedStartDate');
            $table->date('plannedEndDate');
            $table->string('description');
            $table->string('requirements', 500)->nullable();
            $table->string('due');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ongoing_payments');
    }
}
