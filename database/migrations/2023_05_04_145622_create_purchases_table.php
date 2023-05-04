<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('id_transaction');
            $table->foreignId('user_id')->constrained('users');
            //->datos de envio:
            $table->string('address');
            $table->string('city');
            $table->string('region');
            $table->string('phone');
            $table->string('payment_method');
            $table->string('amount_in_cents');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchases');
    }
};
