@extends('admin.layouts.app')

@section('title', 'Order Details')
@section('page-title', 'Order #' . $id)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders') }}">Orders</a></li>
    <li class="breadcrumb-item active">Order #{{ $id }}</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Order Details</h3>
        </div>
        <div class="card-body">
            <p>Order details for order #{{ $id }} will be implemented here.</p>
        </div>
    </div>
@endsection
