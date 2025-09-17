@extends('web.layouts.app')

@section('content')
<section class="skc-section" style="padding-top: 20px; background: #f8f8f8; min-height: 100vh;">
    <div class="skc-container">
        <!-- Page Header -->
        <div style="background: white; border-radius: 12px; padding: 40px; margin-bottom: 40px; text-align: center; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
            <h1 style="font-size: 36px; font-weight: 700; color: var(--skc-black); margin: 0 0 15px 0;">Special Combo Offers</h1>
            <p style="font-size: 18px; color: var(--skc-medium-gray); margin: 0;">Save big with our exclusive combo deals</p>
        </div>

        <!-- Combo Offers Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin-bottom: 60px;">
            @forelse($combos as $combo)
                <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s; hover: transform: translateY(-5px);">
                    <a href="{{ route('combo.show', $combo->slug) }}" style="text-decoration: none;">
                        <div style="position: relative; height: 250px; background: linear-gradient(135deg, #f69d1c 0%, #ff8c00 100%); display: flex; align-items: center; justify-content: center;">
                            @if($combo->image_path)
                                <img src="{{ Storage::url($combo->image_path) }}" alt="{{ $combo->name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="text-align: center; color: white;">
                                    <i class="fas fa-gift" style="font-size: 64px; margin-bottom: 15px;"></i>
                                    <p style="font-weight: 600; font-size: 20px;">Combo Deal</p>
                                </div>
                            @endif
                            
                            @if($combo->discount_percentage > 0)
                                <div style="position: absolute; top: 20px; right: 20px; background: #ff4444; color: white; padding: 8px 16px; border-radius: 25px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                    {{ $combo->discount_percentage }}% OFF
                                </div>
                            @endif
                            
                            @if($combo->ends_at)
                                <div style="position: absolute; bottom: 20px; left: 20px; background: rgba(0,0,0,0.7); color: white; padding: 6px 12px; border-radius: 6px; font-size: 13px;">
                                    <i class="fas fa-clock"></i> Valid until {{ $combo->ends_at->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                        
                        <div style="padding: 25px;">
                            <h3 style="font-size: 22px; font-weight: 700; color: var(--skc-black); margin: 0 0 12px 0;">{{ $combo->name }}</h3>
                            <p style="color: var(--skc-medium-gray); font-size: 15px; margin: 0 0 20px 0; line-height: 1.6;">{{ $combo->description }}</p>
                            
                            <!-- Products in combo -->
                            <div style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                                <p style="font-size: 14px; font-weight: 600; color: var(--skc-black); margin: 0 0 10px 0;">
                                    <i class="fas fa-box" style="margin-right: 8px; color: var(--skc-orange);"></i>
                                    Items in this combo:
                                </p>
                                <ul style="margin: 0; padding-left: 20px;">
                                    @foreach($combo->products->take(3) as $product)
                                        <li style="color: var(--skc-medium-gray); font-size: 14px; margin-bottom: 5px;">{{ $product->name }}</li>
                                    @endforeach
                                    @if($combo->products->count() > 3)
                                        <li style="color: var(--skc-orange); font-size: 14px; font-weight: 600;">+{{ $combo->products->count() - 3 }} more items</li>
                                    @endif
                                </ul>
                            </div>
                            
                            <!-- Pricing -->
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <div>
                                    @if($combo->original_price > $combo->combo_price)
                                        <span style="text-decoration: line-through; color: #999; font-size: 16px;">₹{{ number_format($combo->original_price, 0) }}</span>
                                    @endif
                                    <span style="font-size: 28px; font-weight: 700; color: var(--skc-orange); margin-left: 10px;">₹{{ number_format($combo->combo_price, 0) }}</span>
                                </div>
                                @if($combo->savings > 0)
                                    <span style="background: #e8f5e9; color: #2e7d32; padding: 6px 14px; border-radius: 6px; font-size: 14px; font-weight: 600;">
                                        Save ₹{{ number_format($combo->savings, 0) }}
                                    </span>
                                @endif
                            </div>
                            
                            <!-- View Details Button -->
                            <button style="width: 100%; padding: 14px; background: var(--skc-black); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 16px; cursor: pointer; transition: all 0.3s;">
                                View Details
                            </button>
                        </div>
                    </a>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <i class="fas fa-gift" style="font-size: 64px; color: #ddd; margin-bottom: 20px;"></i>
                    <h3 style="font-size: 24px; color: var(--skc-medium-gray); margin: 0 0 10px 0;">No Combo Offers Available</h3>
                    <p style="color: var(--skc-medium-gray);">Check back soon for exciting combo deals!</p>
                    <a href="{{ route('products') }}" style="display: inline-block; margin-top: 20px; padding: 12px 30px; background: var(--skc-orange); color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                        Browse Products
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($combos->hasPages())
            <div style="display: flex; justify-content: center; margin-bottom: 40px;">
                {{ $combos->links() }}
            </div>
        @endif
    </div>
</section>
@endsection