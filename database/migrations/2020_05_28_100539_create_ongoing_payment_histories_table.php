<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOngoingPaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // ToDO: delete realPaymentDate to not have problems with filtering for date
    public function up()
    {
        Schema::create('ongoing_payment_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('ongoing_payment_id');
            $table->smallInteger('grantedFunds');
            $table->smallInteger('spentFunds')->nullable();
            $table->date('plannedPaymentDate');
            $table->date('realPaymentDate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ongoing_payment_histories');
    }
}
