
@extends('web.layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="skc-hero-section" style="height: 300px;">
        <div class="skc-hero-slider">
            <div class="skc-hero-slide active">
                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1600" alt="Order Details" class="skc-hero-image">
                <div class="skc-hero-content">
                    <h1 class="skc-hero-title">Order #{{ $order->id }}</h1>
                    <p class="skc-hero-subtitle">Order details and tracking information</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Order Details Section -->
    <section class="skc-section">
        <div class="skc-container">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px;">
                <!-- Order Items -->
                <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden;">
                    <div style="padding: 30px; border-bottom: 1px solid var(--skc-border);">
                        <h2 style="font-size: 28px; font-weight: 700; color: var(--skc-black);">Order Items</h2>
                    </div>
                    
                    <div>
                        @foreach($order->items as $item)
                            <div style="padding: 30px; border-bottom: 1px solid var(--skc-border);">
                                <div style="display: flex; gap: 20px;">
                                    <!-- Product Image -->
                                    <div style="flex-shrink: 0;">
                                        @if($item->product && $item->product->first_image)
                                            <img src="{{ asset('storage/' . $item->product->first_image) }}" alt="{{ $item->name }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px;">
                                        @else
                                            <div style="width: 100px; height: 100px; background: var(--skc-light-gray); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image" style="font-size: 30px; color: var(--skc-medium-gray);"></i>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div style="flex: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: start;">
                                            <div>
                                                <h3 style="font-size: 20px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">
                                                    {{ $item->name }}
                                                </h3>
                                                @if($item->variant)
                                                    <p style="font-size: 14px; color: var(--skc-medium-gray); margin-bottom: 5px;">Size: {{ $item->variant->name }}</p>
                                                @endif
                                                @if($item->addons_json && count($item->addons_json) > 0)
                                                    <div style="margin-top: 8px;">
                                                        <span style="font-size: 14px; font-weight: 600; color: var(--skc-black);">Add-ons:</span>
                                                        @foreach($item->addons_json as $addon)
                                                            <span style="display: inline-block; background: var(--skc-light-gray); color: var(--skc-black); padding: 4px 8px; border-radius: 6px; font-size: 12px; margin: 2px;">
                                                                {{ $addon['name'] ?? $addon }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                <div style="margin-top: 10px;">
                                                    <span style="font-size: 14px; color: var(--skc-medium-gray);">Unit Price: </span>
                                                    <span style="font-size: 14px; font-weight: 600; color: var(--skc-black);">₹{{ number_format($item->price, 2) }}</span>
                                                </div>
                                                <div style="margin-top: 5px;">
                                                    <span style="font-size: 14px; color: var(--skc-medium-gray);">Quantity: </span>
                                                    <span style="font-size: 14px; font-weight: 600; color: var(--skc-black);">{{ $item->qty }}</span>
                                                </div>
                                            </div>

                                            <!-- Price -->
                                            <div style="text-align: right;">
                                                <div style="font-size: 12px; color: var(--skc-medium-gray); margin-bottom: 5px;">Total</div>
                                                <div style="font-size: 20px; font-weight: 700; color: var(--skc-orange);">
                                                    ₹{{ number_format($item->line_total, 2) }}
                                                </div>
                                                @if($item->discount_amount && $item->discount_amount > 0)
                                                    <div style="font-size: 12px; color: #059669; margin-top: 5px;">
                                                        Saved ₹{{ number_format($item->discount_amount, 2) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Order Summary -->
                <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 30px; height: fit-content;">
                    <h2 style="font-size: 24px; font-weight: 700; color: var(--skc-black); margin-bottom: 25px;">Order Summary</h2>
                    
                    <!-- Order Status -->
                    <div style="margin-bottom: 25px;">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin-bottom: 10px;">Order Status</h3>
                        @php
                            $status = strtolower($order->status);
                            $statusStyles = [
                                'pending' => 'background: #fef3c7; color: #92400e;',
                                'processing' => 'background: #dbeafe; color: #1e40af;',
                                'completed' => 'background: #d1fae5; color: #065f46;',
                                'cancelled' => 'background: #fee2e2; color: #991b1b;',
                            ];
                        @endphp
                        <span style="display: inline-flex; align-items: center; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; {{ $statusStyles[$status] ?? 'background: #f3f4f6; color: #374151;' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    
                    <!-- Payment Status -->
                    <div style="margin-bottom: 25px;">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin-bottom: 10px;">Payment Status</h3>
                        @php
                            $pay = strtolower($order->payment_status);
                            $payStyles = [
                                'paid' => 'background: #d1fae5; color: #065f46;',
                                'pending' => 'background: #fef3c7; color: #92400e;',
                                'failed' => 'background: #fee2e2; color: #991b1b;',
                                'refunded' => 'background: #f3f4f6; color: #374151;',
                            ];
                        @endphp
                        <span style="display: inline-flex; align-items: center; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 600; {{ $payStyles[$pay] ?? 'background: #f3f4f6; color: #374151;' }}">
                            {{ strtoupper($order->payment_status) }}
                        </span>
                    </div>
                    
                    <!-- Order Details -->
                    <div style="margin-bottom: 25px;">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin-bottom: 15px;">Order Details</h3>
                        <div style="space-y: 3;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--skc-medium-gray);">Order Date</span>
                                <span style="font-weight: 600; color: var(--skc-black);">{{ $order->created_at->format('d M Y') }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                                <span style="color: var(--skc-medium-gray);">Order Time</span>
                                <span style="font-weight: 600; color: var(--skc-black);">{{ $order->created_at->format('h:i A') }}</span>
                            </div>
                            @if($order->delivery_date)
                                <div style="display: flex; justify-content: space-between; margin-top: 10px;">
                                    <span style="color: var(--skc-medium-gray);">Delivery Date</span>
                                    <span style="font-weight: 600; color: var(--skc-black);">{{ $order->delivery_date->format('d M Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div style="border-top: 2px solid var(--skc-border); padding-top: 20px;">
                        <div style="space-y: 3; margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: var(--skc-medium-gray);">Subtotal</span>
                                <span style="font-weight: 600; color: var(--skc-black);">₹{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->tax > 0)
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--skc-medium-gray);">Tax</span>
                                    <span style="font-weight: 600; color: var(--skc-black);">₹{{ number_format($order->tax, 2) }}</span>
                                </div>
                            @endif
                            @if($order->shipping > 0)
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="color: var(--skc-medium-gray);">Shipping</span>
                                    <span style="font-weight: 600; color: var(--skc-black);">₹{{ number_format($order->shipping, 2) }}</span>
                                </div>
                            @endif
                            @if($order->discount > 0)
                                <div style="display: flex; justify-content: space-between; color: #059669;">
                                    <span>Discount</span>
                                    <span style="font-weight: 600;">-₹{{ number_format($order->discount, 2) }}</span>
                                </div>
                            @endif
                            <div style="border-top: 1px solid var(--skc-border); padding-top: 10px; margin-top: 10px;">
                                <div style="display: flex; justify-content: space-between;">
                                    <span style="font-size: 18px; font-weight: 700; color: var(--skc-black);">Total</span>
                                    <span style="font-size: 20px; font-weight: 700; color: var(--skc-orange);">₹{{ number_format($order->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
  
                    <!-- Actions -->
                    <div style="margin-top: 25px;">
                        @if(method_exists($order,'isCancelableByUser') && $order->isCancelableByUser())
                            <form method="POST" action="{{ route('account.orders.cancel', $order->id) }}" style="margin-bottom: 15px;">
                                @csrf
                                <button type="submit" 
                                        style="width: 100%; padding: 12px 24px; background: white; color: #dc2626; border: 2px solid #dc2626; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                        onclick="return confirm('Are you sure you want to cancel this order?')">
                                    Cancel Order
                                </button>
                            </form>
                        @endif
                        
                        <a href="{{ route('account.orders') }}" 
                           style="width: 100%; padding: 12px 24px; background: var(--skc-light-gray); color: var(--skc-black); border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.2s; text-decoration: none; display: block; text-align: center;">
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Delivery Address Section -->
            @if($order->address)
                <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); padding: 30px; margin-top: 40px;">
                    <h2 style="font-size: 24px; font-weight: 700; color: var(--skc-black); margin-bottom: 20px;">Delivery Information</h2>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                        <div>
                            <h3 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin-bottom: 15px;">Delivery Address</h3>
                            <div style="color: var(--skc-medium-gray); line-height: 1.6;">
                                <p style="font-weight: 600; color: var(--skc-black); margin-bottom: 5px;">{{ $order->address->name ?? $order->customer_name }}</p>
                                <p>{{ $order->address->address_line_1 ?? $order->shipping_address }}</p>
                                @if($order->address && $order->address->address_line_2)
                                    <p>{{ $order->address->address_line_2 }}</p>
                                @endif
                                <p>{{ $order->address->city ?? '' }} {{ $order->address->state ?? '' }} {{ $order->address->pincode ?? '' }}</p>
                                @if($order->address && $order->address->phone)
                                    <p style="margin-top: 10px;">
                                        <i class="fas fa-phone" style="margin-right: 5px;"></i> 
                                        {{ $order->address->phone }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h3 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin-bottom: 15px;">Payment Method</h3>
                            <div style="color: var(--skc-medium-gray);">
                                <p style="font-weight: 600; color: var(--skc-black);">
                                    @if($order->payment_method == 'cod')
                                        Cash on Delivery
                                    @elseif($order->payment_method == 'online')
                                        Online Payment
                                    @else
                                        {{ ucfirst($order->payment_method) }}
                                    @endif
                                </p>
                                @if($order->transaction_id)
                                    <p style="margin-top: 5px;">Transaction ID: {{ $order->transaction_id }}</p>
                                @endif
                            </div>
                            
                            @if($order->notes)
                                <div style="margin-top: 20px;">
                                    <h3 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin-bottom: 10px;">Order Notes</h3>
                                    <p style="color: var(--skc-medium-gray);">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection

