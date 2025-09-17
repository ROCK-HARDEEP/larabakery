@extends('admin.layouts.app')

@section('title', 'Product Details')
@section('page-title', 'Product #' . $id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.products') }}">Products</a></li>
    <li class="breadcrumb-item active">Product #{{ $id }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Product Details</h3>
        </div>
        <div class="card-body">
            <p>Product details for product #{{ $id }} will be implemented here.</p>
        </div>
    </div>
@endsection
