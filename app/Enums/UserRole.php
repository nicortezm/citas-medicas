<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case PATIENT = 'patient';
    case DOCTOR = 'doctor';
}
