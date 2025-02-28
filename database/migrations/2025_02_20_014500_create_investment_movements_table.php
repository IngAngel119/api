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
        Schema::create('investment_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->float('amount', 15, 2);
            $table->string('inversion_type');
            $table->dateTime('payment_date');
            $table->integer('created_by')->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_movements');
    }
};
