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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained(); // kasir
            $table->string('invoice_number')->unique();
            $table->date('transaction_date');
            $table->date('due_date');
            $table->string('status'); // diterima, proses, selesai, diambil, batal
            $table->text('notes')->nullable();
            $table->integer('total_price');
            $table->integer('discount')->default(0);
            $table->integer('paid_amount')->default(0);
            $table->integer('change')->default(0);
            $table->string('payment_status'); // belum bayar, dp, lunas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
