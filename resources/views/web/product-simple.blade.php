@extends('web.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
    <p class="text-xl mb-4">Price: ₹{{ number_format($product->base_price, 2) }}</p>
    
    @if($product->description)
        <p class="mb-6">{{ $product->description }}</p>
    @endif
    
    <button class="bg-blue-500 text-white px-6 py-2 rounded">Add to Cart</button>
</div>
@endsection