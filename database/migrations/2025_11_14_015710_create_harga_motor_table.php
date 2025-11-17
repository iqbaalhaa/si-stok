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
        Schema::create('harga_motor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_id')->constrained('motor')->onDelete('cascade');
            $table->decimal('harga_cash', 15, 2);
            $table->decimal('uang_muka', 15, 2)->nullable();
            $table->decimal('angsuran', 15, 2)->nullable();
            $table->integer('lama_kredit')->nullable(); // bulan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('harga_motor');
    }
};
