<?php

namespace App\Helpers;

use App\Enums\AppointmentShift;
use Carbon\Carbon;

class AppointmentHelper
{
  public static function calculateTurn(string $datetime): array|null
  {
    $time = Carbon::parse($datetime);
    $hour = (int) $time->format('H');
    $minutes = (int) $time->format('i');

    // Determinar turno y shift
    if ($hour >= 7 && $hour < 12) {
      $shift = AppointmentShift::MORNING;
      $turnBase = ($hour - 7) * 2 + 1;
    } elseif ($hour >= 14 && $hour < 18) {
      $shift = AppointmentShift::AFTERNOON;
      $turnBase = ($hour - 14) * 2 + 1;
    } else {
      return null; // Hora fuera de rango
    }

    // Ajustar por minutos (si es más de 30 minutos, es el siguiente turno)
    $turn = $minutes >= 30 ? $turnBase + 1 : $turnBase;

    // Validar límites de turnos
    $maxTurns = $shift === AppointmentShift::MORNING ? 10 : 8;
    if ($turn > $maxTurns) {
      return null;
    }

    return [
      'shift' => $shift->value,
      'turn_number' => $turn
    ];
  }

  public static function getTurnDateTime(string $date, string $shift, int $turnNumber): string
  {
    $times = [
      AppointmentShift::MORNING->value => [
        1 => '07:00',
        2 => '07:30',
        3 => '08:00',
        4 => '08:30',
        5 => '09:00',
        6 => '09:30',
        7 => '10:00',
        8 => '10:30',
        9 => '11:00',
        10 => '11:30',
      ],
      AppointmentShift::AFTERNOON->value => [
        1 => '14:00',
        2 => '14:30',
        3 => '15:00',
        4 => '15:30',
        5 => '16:00',
        6 => '16:30',
        7 => '17:00',
        8 => '17:30',
      ],
    ];

    $time = $times[$shift][$turnNumber] ?? null;
    return $time ? "{$date} {$time}" : null;
  }
}