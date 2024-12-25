<?php

namespace App\Observers;

use App\Enums\AppointmentState;
use App\Models\Payment;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        // Verificamos si el estado de la payment cambió a 'approved'
        if ($payment->isDirty('status') && $payment->status === 'approved') {
            // Obtener la cita relacionada con este pago
            $appointment = $payment->appointment;

            // Si existe la cita, actualizar su estado a 'paid'
            if ($appointment) {
                $appointment->status = AppointmentState::PAID;
                $appointment->save();
            }
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
