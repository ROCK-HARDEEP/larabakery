@extends('web.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-serif font-bold text-gray-800 mb-6">Checkout - Summary</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">Delivery Date & Slot</h2>
                    <form method="POST" action="{{ route('checkout.summary.next') }}" id="summary-form" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
                            <input type="date" name="delivery_date" required min="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border rounded-lg"/>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Time Slot</label>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-3">
                                    <label class="inline-flex items-center"><input type="radio" name="slot" value="10:00" class="mr-2">Morning 10:00 AM</label>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <label class="inline-flex items-center"><input type="radio" name="slot" value="14:00" class="mr-2">Afternoon 2:00 PM</label>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <label class="inline-flex items-center"><input type="radio" name="slot" value="17:00" class="mr-2">Evening 5:00 PM</label>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <label class="inline-flex items-center"><input type="radio" name="slot" value="custom" class="mr-2">Custom time (10:00 - 18:00)</label>
                                    <input type="time" name="custom_time" min="10:00" max="18:00" class="px-2 py-1 border rounded">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold mb-4">Order Items</h2>
                    <div class="divide-y">
                        @foreach($items as $it)
                            <div class="py-3 flex items-center justify-between">
                                <div>
                                    <div class="font-medium">{{ $it['name'] }}</div>
                                    <div class="text-sm text-gray-500">Qty: {{ $it['qty'] }}</div>
                                </div>
                                <div class="font-semibold">₹{{ number_format($it['price'] * $it['qty'], 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-24">
                    <h2 class="text-xl font-semibold mb-4">Summary</h2>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₹{{ number_format($summary['subtotal'] ?? 0, 2) }}</span></div>
                        @if(($summary['discount'] ?? 0) > 0)
                            <div class="flex justify-between text-green-600"><span>Discount</span><span>-₹{{ number_format($summary['discount'], 2) }}</span></div>
                        @endif
                        <div class="flex justify-between text-gray-600"><span>Shipping</span><span>₹{{ number_format($summary['shipping'] ?? 0, 2) }}</span></div>
                        <div class="border-t pt-2 flex justify-between font-bold"><span>Total</span><span>₹{{ number_format($summary['total'] ?? 0, 2) }}</span></div>
                    </div>
                    <form method="POST" action="{{ route('checkout.coupon') }}" class="flex space-x-2 mb-4">
                        @csrf
                        <input type="text" name="code" placeholder="Coupon code" class="flex-1 px-3 py-2 border rounded-lg">
                        <button class="px-4 py-2 bg-bakery-500 text-white rounded-lg">Apply</button>
                    </form>
                    <button form="summary-form" class="w-full bg-bakery-500 text-white py-3 rounded-lg">Continue</button>
                    <script>
                    document.getElementById('summary-form').addEventListener('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); this.submit(); }});
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Keep Enter submits working
</script>
@endsection


