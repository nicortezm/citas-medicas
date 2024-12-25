<?php

namespace App\Services;

use App\Enums\AppointmentState;
use App\Enums\UserRole;
use App\Exceptions\AppointmentException;
use App\Helpers\AppointmentHelper;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;

class AppointmentService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        // Se podría inyectar un sistema de notificaciones a pacientes/médicos
    }

    public function createAppointment(array $data, User $patient): Appointment|array
    {
        // Obtener el doctor
        $doctor = $this->getDoctorByEmail($data['doctor_email']);

        // Validar turno
        $turnInfo = AppointmentHelper::calculateTurn(datetime: $data['datetime']);
        if (!$turnInfo) {
            throw new AppointmentException('Invalid datetime', 400);
        }

        $date = Carbon::parse($data['datetime'])->format('Y-m-d');

        // Verificar disponibilidad
        if ($this->isTurnOccupied($doctor->id, $date, $turnInfo)) {
            throw new AppointmentException('Este turno ya está ocupado', 422);
        }

        // Crear la cita
        return Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'date' => $date,
            'shift' => $turnInfo['shift'],
            'turn_number' => $turnInfo['turn_number'],
        ]);
    }

    private function getDoctorByEmail(string $email): User
    {
        return User::where('email', $email)
            ->where('role', UserRole::DOCTOR->value)
            ->firstOrFail();
    }

    private function isTurnOccupied(int $doctorId, string $date, array $turnInfo): bool
    {
        return Appointment::where('doctor_id', $doctorId)
            ->where('date', $date)
            ->where('shift', $turnInfo['shift'])
            ->where('turn_number', $turnInfo['turn_number'])
            ->exists();
    }

    public function getAppointment(int $id)
    {
        return Appointment::find($id);
    }

    public function confirmAppointment(Appointment $appointment)
    {
        if ($appointment->status != AppointmentState::PAID) {
            throw new AppointmentException('La cita no está pagada y no se puede confirmar.', 400);
        }
        $appointment->status = AppointmentState::CONFIRMED;
        $appointment->save();
        return $appointment;
    }
}
