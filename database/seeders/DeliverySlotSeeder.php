<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliverySlot;
use Illuminate\Support\Carbon;

class DeliverySlotSeeder extends Seeder
{
    public function run(): void
    {
        $days = [0,1,2]; // today + next 2 days
        foreach ($days as $d) {
            $date = now()->startOfDay()->addDays($d)->toDateString();
            foreach ([['10:00','13:00'], ['16:00','19:00']] as $win) {
                DeliverySlot::firstOrCreate(
                    ['date' => $date, 'start_time' => $win[0], 'end_time' => $win[1]],
                    ['capacity' => 10, 'booked_count' => 0]
                );
            }
        }
    }
}
