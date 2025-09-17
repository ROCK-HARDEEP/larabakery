@extends('web.layouts.app')

@section('content')
<section class="skc-section" style="padding-top: 20px; background: #f8f8f8; min-height: 100vh;">
    <!-- Breadcrumb -->
    <div style="background: white; border-bottom: 1px solid var(--skc-border); margin-bottom: 30px;">
        <div class="skc-container" style="padding: 15px 20px;">
            <nav style="display: flex; align-items: center; gap: 10px; font-size: 14px;">
                <a href="{{ route('home') }}" style="color: var(--skc-medium-gray); text-decoration: none; transition: color 0.2s;">Home</a>
                <span style="color: #ccc;">/</span>
                <a href="{{ route('combos.index') }}" style="color: var(--skc-medium-gray); text-decoration: none; transition: color 0.2s;">Combo Offers</a>
                <span style="color: #ccc;">/</span>
                <span style="color: var(--skc-black); font-weight: 600;">{{ $combo->name }}</span>
            </nav>
        </div>
    </div>

    <div class="skc-container">
        <!-- Main Combo Section -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 40px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0;">
                
                <!-- Combo Image -->
                <div style="background: linear-gradient(135deg, #f69d1c 0%, #ff8c00 100%); padding: 40px; display: flex; align-items: center; justify-content: center;">
                    @if($combo->image_path)
                        <img src="{{ Storage::url($combo->image_path) }}" alt="{{ $combo->name }}" 
                             style="width: 100%; max-width: 500px; height: auto; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
                    @else
                        <div style="text-align: center; color: white;">
                            <i class="fas fa-gift" style="font-size: 120px; margin-bottom: 20px;"></i>
                            <h2 style="font-size: 32px; font-weight: 700;">Special Combo</h2>
                        </div>
                    @endif
                </div>

                <!-- Combo Details -->
                <div style="padding: 40px;">
                    <!-- Title and Badge -->
                    <div style="margin-bottom: 25px;">
                        <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 15px;">
                            <h1 style="font-size: 36px; font-weight: 700; color: var(--skc-black); margin: 0;">{{ $combo->name }}</h1>
                            @if($combo->discount_percentage > 0)
                                <span style="background: #ff4444; color: white; padding: 8px 16px; border-radius: 25px; font-weight: 700; font-size: 16px;">
                                    {{ $combo->discount_percentage }}% OFF
                                </span>
                            @endif
                        </div>
                        @if($combo->ends_at)
                            <p style="color: var(--skc-medium-gray); font-size: 14px; margin: 0;">
                                <i class="fas fa-clock" style="margin-right: 5px;"></i>
                                Valid until {{ $combo->ends_at->format('M d, Y') }}
                            </p>
                        @endif
                    </div>

                    <!-- Description -->
                    <div style="margin-bottom: 30px;">
                        <p style="color: var(--skc-medium-gray); line-height: 1.8; font-size: 16px;">{{ $combo->description }}</p>
                    </div>

                    <!-- Price Section -->
                    <div style="padding: 25px; background: linear-gradient(90deg, rgba(246,157,28,0.1) 0%, rgba(246,157,28,0.05) 100%); border-left: 4px solid var(--skc-orange); margin-bottom: 30px; border-radius: 0 8px 8px 0;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                @if($combo->original_price > $combo->combo_price)
                                    <p style="margin: 0 0 5px 0;">
                                        <span style="color: #999; font-size: 14px;">Original Price:</span>
                                        <span style="text-decoration: line-through; color: #999; font-size: 18px; margin-left: 10px;">₹{{ number_format($combo->original_price, 2) }}</span>
                                    </p>
                                @endif
                                <p style="margin: 0;">
                                    <span style="color: var(--skc-black); font-size: 14px; font-weight: 600;">Combo Price:</span>
                                    <span style="font-size: 36px; font-weight: 700; color: var(--skc-orange); margin-left: 10px;">₹{{ number_format($combo->combo_price, 2) }}</span>
                                </p>
                            </div>
                            @if($combo->savings > 0)
                                <div style="text-align: center; background: white; padding: 15px 20px; border-radius: 8px;">
                                    <p style="font-size: 14px; color: #2e7d32; margin: 0 0 5px 0; font-weight: 600;">You Save</p>
                                    <p style="font-size: 24px; color: #2e7d32; margin: 0; font-weight: 700;">₹{{ number_format($combo->savings, 2) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quantity and Add to Cart -->
                    <div style="display: flex; gap: 20px; align-items: flex-end; margin-bottom: 30px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Quantity</label>
                            <div style="display: flex; align-items: center; border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                                <button onclick="decrementQty()" style="padding: 12px 16px; background: white; border: none; cursor: pointer; transition: background 0.2s;">
                                    <i class="fas fa-minus" style="color: var(--skc-medium-gray);"></i>
                                </button>
                                <input type="number" id="quantity" value="1" min="1" max="{{ $combo->max_quantity ?? 10 }}" 
                                       style="width: 60px; text-align: center; border: none; border-left: 2px solid #e0e0e0; border-right: 2px solid #e0e0e0; padding: 12px 0; font-weight: 600; font-size: 16px;">
                                <button onclick="incrementQty()" style="padding: 12px 16px; background: white; border: none; cursor: pointer; transition: background 0.2s;">
                                    <i class="fas fa-plus" style="color: var(--skc-medium-gray);"></i>
                                </button>
                            </div>
                        </div>
                        
                        <form action="{{ route('cart.add.combo', $combo) }}" method="POST" style="flex: 1;">
                            @csrf
                            <input type="hidden" name="quantity" id="quantityInput" value="1">
                            <button type="submit" style="width: 100%; padding: 16px 32px; background: var(--skc-black); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <i class="fas fa-shopping-cart"></i>
                                Add Combo to Cart
                            </button>
                        </form>
                    </div>

                    <!-- Additional Info -->
                    <div style="display: flex; gap: 30px; padding-top: 25px; border-top: 1px solid #e0e0e0;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-truck" style="color: var(--skc-orange); font-size: 20px;"></i>
                            <span style="color: var(--skc-medium-gray); font-size: 14px;">Free delivery on orders above ₹500</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-shield-alt" style="color: var(--skc-orange); font-size: 20px;"></i>
                            <span style="color: var(--skc-medium-gray); font-size: 14px;">100% Safe & Secure</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products in Combo -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); padding: 40px; margin-bottom: 40px;">
            <h2 style="font-size: 28px; font-weight: 700; color: var(--skc-black); margin: 0 0 30px 0;">Products in this Combo</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 25px;">
                @foreach($combo->products as $product)
                    <div style="text-align: center;">
                        <a href="{{ route('product.show', $product->slug) }}" style="text-decoration: none;">
                            <div style="aspect-ratio: 1/1; background: #f8f9fa; border-radius: 8px; overflow: hidden; margin-bottom: 15px;">
                                @if($product->first_image)
                                    <img src="{{ asset('storage/' . $product->first_image) }}" 
                                         alt="{{ $product->name }}" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image" style="font-size: 48px; color: #ddd;"></i>
                                    </div>
                                @endif
                            </div>
                            <h4 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin: 0 0 5px 0;">{{ $product->name }}</h4>
                            <p style="font-size: 14px; color: var(--skc-medium-gray); margin: 0;">
                                @if(isset($combo->pivot) && $combo->pivot->quantity > 1)
                                    Qty: {{ $combo->pivot->quantity }}
                                @endif
                            </p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Related Combos -->
        @if($relatedCombos && $relatedCombos->count() > 0)
        <div style="margin-bottom: 60px;">
            <h2 style="font-size: 28px; font-weight: 700; color: var(--skc-black); margin: 0 0 30px 0; text-align: center;">More Combo Offers</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
                @foreach($relatedCombos as $relatedCombo)
                    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: all 0.3s;">
                        <a href="{{ route('combo.show', $relatedCombo->slug) }}" style="text-decoration: none;">
                            <div style="position: relative; height: 200px; background: linear-gradient(135deg, #f69d1c 0%, #ff8c00 100%); display: flex; align-items: center; justify-content: center;">
                                @if($relatedCombo->image_path)
                                    <img src="{{ Storage::url($relatedCombo->image_path) }}" alt="{{ $relatedCombo->name }}" 
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div style="text-align: center; color: white;">
                                        <i class="fas fa-gift" style="font-size: 48px; margin-bottom: 10px;"></i>
                                        <p style="font-weight: 600;">Combo Deal</p>
                                    </div>
                                @endif
                                
                                @if($relatedCombo->discount_percentage > 0)
                                    <div style="position: absolute; top: 15px; right: 15px; background: #ff4444; color: white; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 14px;">
                                        {{ $relatedCombo->discount_percentage }}% OFF
                                    </div>
                                @endif
                            </div>
                            
                            <div style="padding: 20px;">
                                <h3 style="font-size: 18px; font-weight: 700; color: var(--skc-black); margin: 0 0 10px 0;">{{ $relatedCombo->name }}</h3>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 22px; font-weight: 700; color: var(--skc-orange);">₹{{ number_format($relatedCombo->combo_price, 0) }}</span>
                                    @if($relatedCombo->savings > 0)
                                        <span style="background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                            Save ₹{{ number_format($relatedCombo->savings, 0) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</section>

<script>
// Quantity controls
function incrementQty() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    const max = parseInt(input.max);
    if (current < max) {
        input.value = current + 1;
        document.getElementById('quantityInput').value = current + 1;
    }
}

function decrementQty() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
        document.getElementById('quantityInput').value = current - 1;
    }
}

// Update hidden input when quantity changes
document.getElementById('quantity').addEventListener('change', function() {
    document.getElementById('quantityInput').value = this.value;
});
</script>
@endsection