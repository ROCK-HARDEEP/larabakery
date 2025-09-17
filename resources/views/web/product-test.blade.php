@extends('web.layouts.app')

@section('content')
<div class="p-8">
    <h1>Test Product Page</h1>
    <p>Product: {{ $product->name }}</p>
    
    @if($product->variants && $product->variants->count() > 0)
        <div>Has variants</div>
    @endif
    
    <div>Always show this section</div>
</div>
@endsection