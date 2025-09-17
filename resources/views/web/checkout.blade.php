
@extends('web.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-serif font-bold text-gray-800 mb-4">Checkout</h1>
            <p class="text-xl text-gray-600">Complete your order with delivery and payment details</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form id="checkout-form" action="{{ route('checkout.place') }}" method="POST" class="space-y-8">
      @csrf
                    
                    <!-- Delivery Details -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-truck text-bakery-500 mr-3"></i>Delivery Details
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', auth()->user()->name ?? '') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bakery-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', auth()->user()->email ?? '') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bakery-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bakery-500 focus:border-transparent">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="line1" class="block text-sm font-medium text-gray-700 mb-2">Address Line *</label>
                                <textarea id="line1" 
                                          name="line1" 
                                          rows="3"
                                          required
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bakery-500 focus:border-transparent">{{ old('line1') }}</textarea>
                            </div>
                            
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                <input type="text" 
                                       id="city" 
                                       name="city" 
                                       value="{{ old('city') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bakery-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="pincode" class="block text-sm font-medium text-gray-700 mb-2">Pincode *</label>
                                <input type="text" 
                                       id="pincode" 
                                       name="pincode" 
                                       value="{{ old('pincode') }}"
                                       required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bakery-500 focus:border-transparent">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="gstin" class="block text-sm font-medium text-gray-700 mb-2">GSTIN (Optional)</label>
                                <input type="text" 
                                       id="gstin" 
                                       name="gstin" 
                                       value="{{ old('gstin') }}"
                                       placeholder="For business customers"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bakery-500 focus:border-transparent">
                            </div>
                        </div>
      </div>
                    
                    <!-- Delivery Slot -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-clock text-bakery-500 mr-3"></i>Delivery Slot
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">Delivery Date *</label>
                                <input type="date" 
                                       id="delivery_date" 
                                       name="delivery_date" 
                                       required
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bakery-500 focus:border-transparent">
      </div>
                            
                            <div>
                                <label for="delivery_slot" class="block text-sm font-medium text-gray-700 mb-2">Time Slot *</label>
                                <select id="delivery_slot" 
                                        name="slot_id" 
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bakery-500 focus:border-transparent">
                                    <option value="">Select time slot</option>
                                    {{-- Slots will be populated via JavaScript when date is selected --}}
        </select>
      </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                <div class="text-sm text-blue-700">
                                    <p class="font-medium">Delivery Information:</p>
                                    <ul class="mt-2 space-y-1">
                                        <li>• Free delivery on orders above ₹500</li>
                                        <li>• Same-day delivery available for orders placed before 2 PM</li>
                                        <li>• Delivery within 2 hours of your selected time slot</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-credit-card text-bakery-500 mr-3"></i>Payment Method
                        </h2>
                        
                        <div class="space-y-4">
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-bakery-300 transition-colors">
                                <input type="radio" name="payment_mode" value="razorpay" class="w-4 h-4 text-bakery-600 border-gray-300 focus:ring-bakery-500" checked>
                                <div class="ml-3 flex items-center">
                                    <img src="https://razorpay.com/favicon.png" alt="Razorpay" class="w-8 h-8 mr-3">
                                    <div>
                                        <div class="font-medium text-gray-800">Pay Online (Razorpay)</div>
                                        <div class="text-sm text-gray-600">Credit/Debit cards, UPI, Net Banking</div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-bakery-300 transition-colors">
                                <input type="radio" name="payment_mode" value="cod" class="w-4 h-4 text-bakery-600 border-gray-300 focus:ring-bakery-500">
                                <div class="ml-3 flex items-center">
                                    <i class="fas fa-money-bill-wave text-2xl text-green-500 mr-3"></i>
                                    <div>
                                        <div class="font-medium text-gray-800">Cash on Delivery</div>
                                        <div class="text-sm text-gray-600">Pay when you receive your order</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Order Notes -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-sticky-note text-bakery-500 mr-3"></i>Order Notes
                        </h2>
                        
                        <div>
                            <label for="order_notes" class="block text-sm font-medium text-gray-700 mb-2">Special Instructions (Optional)</label>
                            <textarea id="order_notes" 
                                      name="order_notes" 
                                      rows="3"
                                      placeholder="Any special requests or delivery instructions..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-bakery-500 focus:border-transparent">{{ old('order_notes') }}</textarea>
                        </div>
      </div>
    </form>
  </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Order Summary</h2>
                    
                    <!-- Cart Items -->
                    <div class="space-y-4 mb-6">
                        @foreach($cartItems ?? [] as $item)
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-800 truncate">{{ $item['product']->name }}</h4>
                                    <p class="text-xs text-gray-500">Qty: {{ $item['quantity'] }}</p>
                                </div>
                                <div class="text-sm font-semibold text-gray-800">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Price Breakdown -->
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal ({{ $totalItems ?? 0 }} items)</span>
                            <span>₹{{ number_format($subtotal ?? 0, 2) }}</span>
                        </div>
                        
                        @if(isset($deliveryFee) && $deliveryFee > 0)
                            <div class="flex justify-between text-gray-600">
                                <span>Delivery Fee</span>
                                <span>₹{{ number_format($deliveryFee, 2) }}</span>
                            </div>
                        @endif
                        
                        @if(isset($discount) && $discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>-₹{{ number_format($discount, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-xl font-bold text-gray-800">
                                <span>Total</span>
                                <span>₹{{ number_format($total ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Place Order Button -->
                    <button type="submit" 
                            form="checkout-form"
                            class="w-full bg-bakery-500 text-white py-4 px-6 rounded-xl text-lg font-semibold hover:bg-bakery-600 transition-colors shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center justify-center">
                        <i class="fas fa-lock mr-2"></i>Place Order
                    </button>
                    
                    <!-- Security Info -->
                    <div class="mt-6 text-center">
                        <div class="flex items-center justify-center space-x-2 text-gray-500 text-sm">
                            <i class="fas fa-shield-alt"></i>
                            <span>100% Secure Checkout</span>
                        </div>
                    </div>
                </div>
      </div>
    </div>
  </div>
</div>

<script>
// Set minimum date to today
document.getElementById('delivery_date').min = new Date().toISOString().split('T')[0];

// Update delivery slots when date changes
document.getElementById('delivery_date').addEventListener('change', function() {
    const date = this.value;
    if (date) {
        // Fetch available slots for the selected date
        fetch('{{ route("checkout.slots") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ date: date })
        })
        .then(response => response.json())
        .then(data => {
            const slotSelect = document.getElementById('delivery_slot');
            slotSelect.innerHTML = '<option value="">Select time slot</option>';
            
            if (data.slots) {
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot.id;
                    option.textContent = slot.label || `Slot ${slot.id}`;
                    if (!slot.available) {
                        option.disabled = true;
                        option.textContent += ' (Full)';
                    }
                    slotSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching slots:', error);
        });
    }
});

// Validate pincode
document.getElementById('pincode').addEventListener('blur', function() {
    const pincode = this.value;
    if (pincode && pincode.length === 6) {
        fetch('{{ route("checkout.pincode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ pincode: pincode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                this.classList.remove('border-red-500');
                this.classList.add('border-green-500');
            } else {
                this.classList.remove('border-green-500');
                this.classList.add('border-red-500');
                alert('Delivery not available for this pincode');
            }
        })
        .catch(error => {
            console.error('Error validating pincode:', error);
        });
    }
  });
</script>
@endsection
