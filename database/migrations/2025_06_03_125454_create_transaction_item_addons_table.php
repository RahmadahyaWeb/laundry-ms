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
        Schema::create('transaction_item_addons', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('transaction_item_id');
            $table->unsignedBigInteger('service_id');
            $table->decimal('price', 10, 2);

            $table->timestamps();

            $table->foreign('transaction_item_id')->references('id')->on('transaction_items')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_item_addons');
    }
};
