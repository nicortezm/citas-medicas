<?php

namespace App\Http\Requests\Appointment;

use App\Enums\UserRole;
use App\Helpers\AppointmentHelper;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === UserRole::PATIENT->value;
    }

    public function rules(): array
    {
        return [
            'doctor_email' => 'required|email|exists:users,email,role,' . UserRole::DOCTOR->value,
            'datetime' => [
                'required',
                'date_format:Y-m-d H:i',
                'after_or_equal:now',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_email.required' => 'El email del doctor es requerido',
            'doctor_email.email' => 'El email del doctor debe ser válido',
            'doctor_email.exists' => 'El doctor no existe en nuestro sistema',
            'datetime.required' => 'La fecha y hora son requeridas',
            'datetime.date_format' => 'El formato debe ser YYYY-MM-DD HH:mm',
            'datetime.after_or_equal' => 'La fecha y hora deben ser posteriores a ahora',
        ];
    }

}