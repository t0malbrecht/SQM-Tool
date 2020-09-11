<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProofOfUsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proof_of_uses', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('grantedClaim_id');
            $table->string('submitter');
            $table->date('submitDate');
            $table->string('document')->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proof_of_uses');
    }
}
