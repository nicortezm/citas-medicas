<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('mp_reference_id')->nullable();
            $table->timestamps();
            // Llaves foráneas
            $table->unsignedBigInteger('appointment_id'); // Relación con el paciente
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
