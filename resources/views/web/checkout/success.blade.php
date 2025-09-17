@extends('web.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto text-center">
        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check text-3xl"></i>
        </div>
        <h1 class="text-4xl font-serif font-bold text-gray-800 mb-4">Order Placed Successfully!</h1>
        <p class="text-gray-600 mb-8">Your order #{{ $order->id }} has been placed. We'll notify you as it moves through delivery.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('account.orders.show', $order->id) }}" class="bg-bakery-500 text-white px-6 py-3 rounded-lg">Track Order</a>
            <a href="{{ route('home') }}" class="px-6 py-3 rounded-lg border">Continue Shopping</a>
        </div>
    </div>
</div>
@endsection


