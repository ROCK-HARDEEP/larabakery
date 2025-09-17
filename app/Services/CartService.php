<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Bundle;

class CartService
{
    const SESSION_KEY = 'cart.items';

    public function items(): array
    {
        return session(self::SESSION_KEY, []);
    }

    public function add(int $productId, ?int $variantId, int $qty = 1, array $addons = []): void
    {
        $items = $this->items();
        
        // Generate unique key based on product, variant, and addons
        $key = $this->generateCartKey($productId, $variantId, $addons);
        
        if (!isset($items[$key])) {
            $product = Product::findOrFail($productId);
            $variant = $variantId ? ProductVariant::find($variantId) : null;
            
            // Determine price: variant price takes precedence over product base price
            $price = $variant ? (float)$variant->price : (float)$product->base_price;
            
            $items[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name' => $product->name . ($variant ? ' - ' . $variant->name : ''),
                'sku' => $variant ? $variant->sku : $product->sku,
                'price' => $price,
                'qty' => 0,
                'addons' => $addons,
            ];
        }
        
        $items[$key]['qty'] += max(1, $qty);
        session([self::SESSION_KEY => $items]);
    }

    public function addBundle(Bundle $bundle, int $qty = 1): void
    {
        $items = $this->items();
        $key = $this->generateBundleCartKey($bundle->id);
        if (!isset($items[$key])) {
            $items[$key] = [
                'type' => 'bundle',
                'bundle_id' => $bundle->id,
                'name' => 'Bundle: ' . $bundle->name,
                'sku' => 'BNDL-' . $bundle->id,
                'price' => (float) $bundle->price,
                'qty' => 0,
            ];
        }
        $items[$key]['qty'] += max(1, $qty);
        session([self::SESSION_KEY => $items]);
    }

    public function update(string $key, int $qty): void
    {
        $items = $this->items();
        if (isset($items[$key])) {
            $items[$key]['qty'] = max(1, $qty);
            session([self::SESSION_KEY => $items]);
        }
    }

    public function remove(string $key): void
    {
        $items = $this->items();
        unset($items[$key]);
        session([self::SESSION_KEY => $items]);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    private function generateCartKey(int $productId, ?int $variantId, array $addons): string
    {
        $addonHash = !empty($addons) ? md5(json_encode($addons)) : 'no-addons';
        return $productId . '-' . ($variantId ?? 0) . '-' . $addonHash;
    }

    private function generateBundleCartKey(int $bundleId): string
    {
        return 'bundle-' . $bundleId;
    }
}