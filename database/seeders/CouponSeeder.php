<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::updateOrCreate(
            ['code' => 'TEST10'],
            ['type' => 'percent', 'value' => 10, 'min_cart' => 300, 'usage_limit' => 100, 'is_active' => true]
        );

        Coupon::updateOrCreate(
            ['code' => 'FLAT50'],
            ['type' => 'flat', 'value' => 50, 'min_cart' => 0, 'usage_limit' => 100, 'is_active' => true]
        );
    }
}