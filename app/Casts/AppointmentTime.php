<?php

namespace App\Casts;

use App\Enums\AppointmentShift;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AppointmentTime implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $shift = $attributes['shift'];
        $turnNumber = $attributes['turn_number'];
        
        $times = [
            AppointmentShift::MORNING->value => [
                1 => ['07:00', '07:30'],
                2 => ['07:30', '08:00'],
                3 => ['08:00', '08:30'],
                4 => ['08:30', '09:00'],
                5 => ['09:00', '09:30'],
                6 => ['09:30', '10:00'],
                7 => ['10:00', '10:30'],
                8 => ['10:30', '11:00'],
                9 => ['11:00', '11:30'],
                10 => ['11:30', '12:00'],
            ],
            AppointmentShift::AFTERNOON->value => [
                1 => ['14:00', '14:30'],
                2 => ['14:30', '15:00'],
                3 => ['15:00', '15:30'],
                4 => ['15:30', '16:00'],
                5 => ['16:00', '16:30'],
                6 => ['16:30', '17:00'],
                7 => ['17:00', '17:30'],
                8 => ['17:30', '18:00'],
            ],
        ];

        $timeRange = $times[$shift][$turnNumber] ?? null;
        
        if (!$timeRange) {
            return null;
        }

        return [
            'start' => Carbon::parse("{$attributes['date']} {$timeRange[0]}"),
            'end' => Carbon::parse("{$attributes['date']} {$timeRange[1]}"),
            'formatted' => "{$timeRange[0]} - {$timeRange[1]}"
        ];
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
