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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // misal: LAUNDRY50
            $table->enum('type', ['nominal', 'percentage']); // jenis diskon
            $table->decimal('value', 10, 2); // nominal potongan atau persentase
            $table->decimal('min_transaction', 10, 2)->nullable(); // minimal transaksi
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('usage_limit')->nullable(); // berapa kali bisa digunakan
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('thumbnail')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
