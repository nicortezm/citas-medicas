<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids;
    protected $fillable = [
        'appointment_id',
        'mp_reference_id',
        'status',
    ];
    protected $appends = ['mp_link'];

    public function getMpLinkAttribute()
    {
        return "https://www.mercadopago.cl/checkout/v1/redirect?pref_id={$this->mp_reference_id}";
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
