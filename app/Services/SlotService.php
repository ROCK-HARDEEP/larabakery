<?php

namespace App\Services;

use App\Models\DeliverySlot;
use Carbon\Carbon;

class SlotService
{
    public function forDate(string $date): array
    {
        return DeliverySlot::where('date', $date)
            ->orderBy('start_time')->get()->map(function($s){
                return [
                    'id' => $s->id,
                    'label' => substr($s->start_time,0,5).' - '.substr($s->end_time,0,5),
                    'available' => $s->capacity > $s->booked_count,
                ];
            })->toArray();
    }
}
