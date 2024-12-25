<?php

namespace App\Services;

use App\Models\Payment;

class PaymentService
{
    /**
     * Create a new class instance.
     */
    public function __construct(MercadoPagoService $mercadoPagoService)
    {
        //
    }

    public function getPayment(string $id)
    {
        $payment = Payment::where("id", $id)->first();
        return $payment;
    }
}
