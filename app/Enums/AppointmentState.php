<?php

namespace App\Enums;

enum AppointmentState: string
{
    case REQUESTED = 'requested';
    case PAID = 'paid';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';

}
