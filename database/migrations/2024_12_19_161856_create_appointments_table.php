<?php

use App\Enums\AppointmentState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('doctor_id');
            $table->date('date');
            $table->enum('shift', ['morning', 'afternoon']);
            $table->integer('turn_number');
            $table->string('status')->default(AppointmentState::REQUESTED->value);
            $table->timestamps();

            $table->foreign('patient_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')  // Previene la eliminación de usuarios con citas
                ->onUpdate('cascade');  // Actualiza las referencias si cambia el ID del usuario

            $table->foreign('doctor_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            // Añadimos índices para mejorar el rendimiento de las consultas frecuentes
            $table->index('date');
            $table->index('status');
            $table->index(['date', 'shift']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
