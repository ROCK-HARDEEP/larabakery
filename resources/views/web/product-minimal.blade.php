@extends('web.layouts.app')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="skc-container py-8">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden p-8">
            <h1 class="text-3xl font-bold">{{ $product->name }}</h1>
            <p>Price: ₹{{ number_format($product->base_price, 2) }}</p>
            
            @if($product->description)
                <p class="mt-4">{{ $product->description }}</p>
            @endif
            
            @if($product->variants && $product->variants->count() > 0)
                <div class="mt-6">
                    <h3>Variants:</h3>
                    @foreach($product->variants as $variant)
                        <div class="border p-2 mt-2">
                            <p>{{ $variant->variant_value }} - ₹{{ number_format($variant->price, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection