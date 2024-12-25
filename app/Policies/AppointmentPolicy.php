<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AppointmentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function viewAppointments(User $user)
    {
        return in_array($user->role, ['admin', 'patient', 'doctor']);
    }
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        // El paciente solo puede ver sus citas.
        if ($user->role === 'patient') {
            return $user->id === $appointment->patient_id;
        }

        // El médico puede ver solo las citas asignadas a él.
        if ($user->role === 'doctor') {
            return $user->id === $appointment->doctor_id;
        }
        return false;
    }


    public function confirm(User $user, Appointment $appointment): bool
    {
        // El médico puede ver solo las citas asignadas a él.
        if ($user->role === 'doctor') {
            return $user->id === $appointment->doctor_id;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Solo los pacientes pueden crear citas.
        return $user->role === 'patient';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // Los médicos pueden modificar solo las citas asignadas a ellos.
        if ($user->role === 'doctor') {
            return $user->id === $appointment->doctor_id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        return false;
    }
}
