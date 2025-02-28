<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_ccs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id');
            $table->float('minimum_payment_amount', 15, 2)->nullable();
            $table->float('interest_free_amount', 15, 2)->nullable();
            $table->float('total_amount', 15, 2);
            $table->date('cut_off_date');
            $table->dateTime('payment_date');
            $table->date('movement_date');
            $table->integer('created_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->foreign('card_id')->references('id')->on('credit_cards');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_tdcs');
    }
};
