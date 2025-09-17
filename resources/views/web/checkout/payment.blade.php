@extends('web.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-serif font-bold text-gray-800 mb-6">Checkout - Payment</h1>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-6">
            <div>
                <h2 class="text-xl font-semibold mb-2">Order Total</h2>
                <div class="text-2xl font-bold">₹{{ number_format($summary['total'] ?? 0, 2) }}</div>
            </div>

            <form id="payment-form" method="POST" action="{{ route('checkout.place') }}" class="space-y-4">
                @csrf
                <div class="space-y-3">
                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer">
                        <input type="radio" name="payment_mode" value="razorpay" class="w-4 h-4" checked>
                        <span class="ml-3">Pay Online (Razorpay)</span>
                    </label>
                    <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer">
                        <input type="radio" name="payment_mode" value="cod" class="w-4 h-4">
                        <span class="ml-3">Cash on Delivery</span>
                    </label>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="{{ route('checkout.address') }}" class="text-bakery-600">Back</a>
                    <button class="bg-bakery-500 text-white px-6 py-2 rounded-lg">Place Order</button>
                </div>
            </form>
            <script>document.getElementById('payment-form').addEventListener('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); this.submit(); }});</script>
        </div>
    </div>
</div>
@endsection


