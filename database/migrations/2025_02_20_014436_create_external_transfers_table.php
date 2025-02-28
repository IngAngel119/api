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
        Schema::create('external_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->float('amount', 15, 2);
            $table->string('reason');
            $table->string('receptor_account');
            $table->string('receiving_bank');
            $table->dateTime('movement_date');
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
        Schema::dropIfExists('external_transfers');
    }
};
