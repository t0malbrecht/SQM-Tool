<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOneTimePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_time_payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('claim_id')->unique();
            $table->unsignedBigInteger('favoredFundsCenter_id');
            $table->unsignedBigInteger('chargedFundsCenter_id');
            $table->unsignedBigInteger('costType_id');
            $table->integer('grantedFunds');
            $table->integer('spentFunds')->nullable();
            $table->date('spentDate')->nullable();
            $table->string('notes', 500)->nullable();
            $table->string('requirements', 500)->nullable();
            $table->date('dueDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('one_time_payments');
    }
}
