<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    /** @use HasFactory<\Database\Factories\AppointmentFactory> */
    use HasFactory;
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date',
        'shift',
        'turn_number',
        'status'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
