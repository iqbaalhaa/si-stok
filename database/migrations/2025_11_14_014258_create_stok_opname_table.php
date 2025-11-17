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
        Schema::create('stok_opname', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_id')->constrained('motors')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('stok_sistem'); // stok sebelum disesuaikan
            $table->integer('stok_fisik');  // hasil pengecekan nyata
            $table->integer('selisih')->nullable(); // stok_fisik - stok_sistem
            $table->text('keterangan')->nullable();
            $table->string('petugas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_opname');
    }
};
