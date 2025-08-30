@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Shopping Cart</h2>

                    @if ($itemCount > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <!-- Cart Items -->
                            <div class="lg:col-span-2">
                                <div class="space-y-4">
                                    @foreach ($cartItems as $item)
                                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                            <div class="flex items-center space-x-4">
                                                <div>
                                                    <h3 class="text-lg font-medium text-gray-900">{{ $item['name'] }}</h3>
                                                    <p class="text-sm text-gray-500">Category:
                                                        {{ $item['category']->name }}</p>
                                                    <p class="text-lg font-medium text-gray-900">
                                                        ${{ number_format($item['price'], 2) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <div class="flex items-center border border-gray-300 rounded">
                                                    <button type="button"
                                                        onclick="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                                        class="px-3 py-1 text-gray-600 hover:text-gray-800 {{ $item['quantity'] <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                        -
                                                    </button>
                                                    <span class="px-3 py-1 text-gray-900">{{ $item['quantity'] }}</span>
                                                    <button type="button"
                                                        onclick="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                                        class="px-3 py-1 text-gray-600 hover:text-gray-800">
                                                        +
                                                    </button>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-lg font-medium text-gray-900">
                                                        ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                                    </p>
                                                </div>
                                                <button type="button" onclick="removeItem({{ $item['id'] }})"
                                                    class="text-red-600 hover:text-red-800">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-6 flex justify-between">
                                    <button type="button" onclick="clearCart()"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        Clear Cart
                                    </button>
                                    <a href="{{ route('admin.products.index') }}"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Continue Shopping
                                    </a>
                                </div>
                            </div>

                            <!-- Cart Summary -->
                            <div class="lg:col-span-1">
                                <div class="bg-gray-50 p-6 rounded-lg">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cart Summary</h3>

                                    <div class="space-y-3">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Items:</span>
                                            <span class="font-medium">{{ $itemCount }}</span>
                                        </div>

                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Subtotal:</span>
                                            <span class="font-medium">${{ number_format($subtotal, 2) }}</span>
                                        </div>

                                        <hr class="my-3">

                                        <div class="flex justify-between text-lg font-bold">
                                            <span>Total:</span>
                                            <span>${{ number_format($subtotal, 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <a href="{{ route('checkout.index') }}"
                                            class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg block text-center">
                                            Proceed to Checkout
                                        </a>
                                    </div>
                                </div>

                                <!-- Available Coupons Info -->
                                @php
                                    $couponService = app(\App\Services\CouponService::class);
                                    $availableCoupons = $couponService->getApplicableCoupons($cartItems);
                                @endphp

                                @if ($availableCoupons->count() > 0)
                                    <div class="mt-4 bg-blue-50 p-4 rounded-lg">
                                        <h4 class="text-sm font-medium text-blue-900 mb-2">Available Coupons</h4>
                                        <div class="space-y-2">
                                            @foreach ($availableCoupons as $coupon)
                                                <div class="text-xs text-blue-700">
                                                    <strong>{{ $coupon->code }}</strong>: {{ $coupon->name }}
                                                    <br>
                                                    <span class="text-blue-600">
                                                        Valid for: {{ $coupon->categories->pluck('name')->implode(', ') }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="text-xs text-blue-600 mt-2">
                                            <strong>Note:</strong> Coupons only apply when ALL products in your cart belong
                                            to the eligible categories.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Your cart is empty</h3>
                            <p class="text-gray-500 mb-6">Looks like you haven't added any products to your cart yet.</p>
                            <a href="{{ route('admin.products.index') }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateQuantity(productId, quantity) {
            if (quantity < 0) return;

            fetch('{{ route('cart.update') }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the cart.');
                });
        }

        function removeItem(productId) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }

            fetch('{{ route('cart.remove') }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing the item.');
                });
        }

        function clearCart() {
            if (!confirm('Are you sure you want to clear your entire cart?')) {
                return;
            }

            fetch('{{ route('cart.clear') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while clearing the cart.');
                });
        }
    </script>
@endpush
