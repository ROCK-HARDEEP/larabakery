<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->unique()->nullable()->after('id');
        });

        // Generate numeric order numbers for existing orders
        $orders = DB::table('orders')->orderBy('created_at')->orderBy('id')->get();
        $ordersByDate = [];

        foreach ($orders as $order) {
            $date = date('Ymd', strtotime($order->created_at));

            if (!isset($ordersByDate[$date])) {
                $ordersByDate[$date] = 0;
            }

            $ordersByDate[$date]++;
            $sequentialNumber = str_pad($ordersByDate[$date], 2, '0', STR_PAD_LEFT);

            // If more than 99 orders on same day, don't pad
            if ($ordersByDate[$date] > 99) {
                $sequentialNumber = $ordersByDate[$date];
            }

            DB::table('orders')
                ->where('id', $order->id)
                ->update([
                    'order_number' => $date . $sequentialNumber
                ]);
        }

        // Make order_number not nullable after populating existing records
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_number')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_number');
        });
    }
};