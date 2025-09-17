<?php
namespace App\Services;

use App\Models\Coupon;

class PricingService
{
    public function summarize(array $items, ?string $couponCode = null, float $taxRate = 0): array
    {
        $subtotal = 0.0; $tax = 0.0; $discount = 0.0; $shipping = 0.0; // free shipping baseline
        foreach ($items as $it) {
            $subtotal += (float)$it['price'] * (int)$it['qty'];
        }
        $tax = round($subtotal * ($taxRate/100), 2);

        $coupon = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValidForAmount($subtotal)) {
                if ($coupon->type === 'flat') $discount = (float)$coupon->value;
                if ($coupon->type === 'percent') $discount = round($subtotal * ((float)$coupon->value/100), 2);
            }
        }
        $total = max(0, round($subtotal + $tax + $shipping - $discount, 2));
        return compact('subtotal','tax','discount','shipping','total');
    }
}