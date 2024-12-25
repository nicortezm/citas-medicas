<?php

namespace App\Http\Controllers;

use App\Services\AppointmentService;
use App\Services\MercadoPagoService;
use App\Services\PaymentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        private AppointmentService $appointmentService,
        private MercadoPagoService $mercadoPagoService,
        private PaymentService $paymentService,
    ) {
    }
    public function getPaymentlink(Request $request, $id)
    {
        $appointment = $this->appointmentService->getAppointment($id);
        $this->authorize('view', $appointment);
        $preference = $this->mercadoPagoService->getPreference($request->user(), $appointment);
        return $preference;
    }

    public function callbackMPSuccess(Request $request)
    {
        $data = $request->query();
        $payment = $this->paymentService->getPayment($data['external_reference']);
        if (!$payment) {
            return response()->json(['message' => 'error connecting to MP service'], 400);
        }
        $payment->status = $data['status'];
        $payment->save();
        return response()->json(['message' => 'Operación completada con éxito'], 200);

    }

    public function callbackMPFailure(Request $request)
    {
        return 'Failure';
    }
}
