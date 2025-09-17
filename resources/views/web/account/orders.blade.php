
@extends('web.layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="skc-hero-section" style="height: 300px;">
        <div class="skc-hero-slider">
            <div class="skc-hero-slide active">
                <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=1600" alt="My Orders" class="skc-hero-image">
                <div class="skc-hero-content">
                    <h1 class="skc-hero-title">My Orders</h1>
                    <p class="skc-hero-subtitle">Track your orders and view order history</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Orders Section -->
    <section class="skc-section">
        <div class="skc-container">
            <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden;">
                <div style="padding: 30px; border-bottom: 1px solid var(--skc-border);">
                    <h2 style="font-size: 28px; font-weight: 700; color: var(--skc-black);">Order History</h2>
                </div>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: var(--skc-light-gray);">
                                <th style="padding: 20px; text-align: left; font-size: 14px; font-weight: 600; color: var(--skc-black); text-transform: uppercase; letter-spacing: 0.5px;">Order #</th>
                                <th style="padding: 20px; text-align: left; font-size: 14px; font-weight: 600; color: var(--skc-black); text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                                <th style="padding: 20px; text-align: left; font-size: 14px; font-weight: 600; color: var(--skc-black); text-transform: uppercase; letter-spacing: 0.5px;">Total</th>
                                <th style="padding: 20px; text-align: left; font-size: 14px; font-weight: 600; color: var(--skc-black); text-transform: uppercase; letter-spacing: 0.5px;">Payment</th>
                                <th style="padding: 20px; text-align: left; font-size: 14px; font-weight: 600; color: var(--skc-black); text-transform: uppercase; letter-spacing: 0.5px;">Date</th>
                                <th style="padding: 20px; text-align: right; font-size: 14px; font-weight: 600; color: var(--skc-black); text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $o)
                                <tr style="border-bottom: 1px solid var(--skc-border); transition: background-color 0.2s;">
                                    <td style="padding: 20px; font-size: 16px; font-weight: 600; color: var(--skc-black);">#{{ $o->id }}</td>
                                    <td style="padding: 20px;">
                                        @php
                                            $status = strtolower($o->status);
                                            $statusStyles = [
                                                'pending' => 'background: #fef3c7; color: #92400e;',
                                                'processing' => 'background: #dbeafe; color: #1e40af;',
                                                'completed' => 'background: #d1fae5; color: #065f46;',
                                                'cancelled' => 'background: #fee2e2; color: #991b1b;',
                                            ];
                                        @endphp
                                        <span style="display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; {{ $statusStyles[$status] ?? 'background: #f3f4f6; color: #374151;' }}">
                                            {{ ucfirst($o->status) }}
                                        </span>
                                    </td>
                                    <td style="padding: 20px; font-size: 18px; font-weight: 700; color: var(--skc-orange);">₹{{ number_format($o->total, 2) }}</td>
                                    <td style="padding: 20px;">
                                        @php
                                            $pay = strtolower($o->payment_status);
                                            $payStyles = [
                                                'paid' => 'background: #d1fae5; color: #065f46;',
                                                'pending' => 'background: #fef3c7; color: #92400e;',
                                                'failed' => 'background: #fee2e2; color: #991b1b;',
                                                'refunded' => 'background: #f3f4f6; color: #374151;',
                                            ];
                                        @endphp
                                        <span style="display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; {{ $payStyles[$pay] ?? 'background: #f3f4f6; color: #374151;' }}">
                                            {{ strtoupper($o->payment_status) }}
                                        </span>
                                    </td>
                                    <td style="padding: 20px; font-size: 14px; color: var(--skc-medium-gray);">{{ $o->created_at->format('d M Y') }}</td>
                                    <td style="padding: 20px; text-align: right;">
                                        <a href="{{ route('account.orders.show', $o->id) }}" 
                                           style="display: inline-block; padding: 8px 16px; background: var(--skc-orange); color: white; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 600; transition: all 0.2s;">
                                            View
                                        </a>
                                        @if(method_exists($o,'isCancelableByUser') && $o->isCancelableByUser())
                                            <form method="POST" action="{{ route('account.orders.cancel', $o->id) }}" style="display: inline-block; margin-left: 10px;">
                                                @csrf
                                                <button type="submit" 
                                                        style="padding: 8px 16px; background: white; color: #dc2626; border: 1px solid #dc2626; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;"
                                                        onclick="return confirm('Are you sure you want to cancel this order?')">
                                                    Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding: 60px 20px; text-align: center;">
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 20px;">
                                            <div style="width: 80px; height: 80px; background: var(--skc-light-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-receipt" style="font-size: 30px; color: var(--skc-medium-gray);"></i>
                                            </div>
                                            <div>
                                                <h3 style="font-size: 20px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px;">No Orders Yet</h3>
                                                <p style="color: var(--skc-medium-gray); font-size: 16px;">You haven't placed any orders yet.</p>
                                            </div>
                                            <a href="{{ route('products') }}" 
                                               style="display: inline-block; padding: 12px 24px; background: var(--skc-orange); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.2s;">
                                                Start Shopping
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div style="padding: 30px; background: var(--skc-light-gray); border-top: 1px solid var(--skc-border);">
                        @include('web.components.custom-pagination', ['paginator' => $orders, 'elements' => $orders->links()->elements])
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
