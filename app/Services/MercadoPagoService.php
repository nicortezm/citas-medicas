<?php

namespace App\Services;
use App\Models\Appointment;
use App\Models\Payment;
use App\Models\User;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Resources\Preference\Item;
use MercadoPago\Resources\Preference\Payer;

class MercadoPagoService
{
    /**
     * Create a new class instance.
     */
    private $appointment_value;
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
        $this->appointment_value = 20000; // Consulta con valor fijo.
    }


    private function createPreference(User $patient, Appointment $appointment)
    {

        // Fill the data about the product(s) being pruchased
        $product = [
            "id" => "1234567890",
            "title" => "Cita médica",
            "description" => "Cita médica para {$patient->name} para la fecha {$appointment->date}",
            "currency_id" => "CLP",
            "quantity" => 1,
            "unit_price" => $this->appointment_value
        ];


        // Mount the array of products that will integrate the purchase amount
        $items = [$product];

        // Retrieve information about the user (use your own function)
        $payer = [
            'name' => $patient->name,
            'email' => $patient->email,
            // 
        ];

        // Create Payment

        $payment = $this->createPayment($appointment);

        // Create the request object to be sent to the API when the preference is created
        $request = $this->createPreferenceRequest($items, $payer, $payment);

        // Instantiate a new Preference Client
        $client = new PreferenceClient();
        try {
            // Send the request that will create the new preference for user's checkout flow
            $preference = $client->create($request);
            // Useful props you could use from this object is 'init_point' (URL to Checkout Pro) or the 'id'
            $payment->mp_reference_id = $preference->id;
            $payment->save();
            // $payment->mp_link = $preference->init_point;
            return $payment;
        } catch (MPApiException $error) {
            // Here you might return whatever your app needs.
            // We are returning null here as an example.
            return null;
        }
    }

    private function createPreferenceRequest($items, $payer, $payment): array
    {
        $paymentMethods = [
            "excluded_payment_methods" => [],
            "installments" => 12,
            "default_installments" => 1
        ];

        $backUrls = [
            'success' => config('mercadopago.success_url'),
            'failure' => config('mercadopago.failure_url'),
        ];

        $request = [
            "items" => $items,
            "payer" => $payer,
            "payment_methods" => $paymentMethods,
            "back_urls" => $backUrls,
            "statement_descriptor" => "NAME_DISPLAYED_IN_USER_BILLING",
            "external_reference" => $payment->id, // must be uuid payment
            "expires" => false,
            "auto_return" => 'approved',
        ];
        return $request;
    }

    private function createPayment($appointment)
    {
        $payment = Payment::create([
            'appointment_id' => $appointment->id
        ]);

        return $payment;
    }

    public function getPreference(User $patient, Appointment $appointment)
    {
        $payment = Payment::where('appointment_id', $appointment->id)->where('status', '!=', 'rejected')->first();
        if ($payment) {
            // $payment->mp_link
            return $payment;
        }
        return $this->createPreference($patient, $appointment);
    }
}
