@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Checkout</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Order Summary -->
                        <div class="lg:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>

                            <div class="space-y-4">
                                @foreach ($cartItems as $item)
                                    <div class="flex justify-between items-center py-3 border-b border-gray-200">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $item['name'] }}</h4>
                                            <p class="text-sm text-gray-500">Qty: {{ $item['quantity'] }}</p>
                                            @if (isset($item['category']))
                                                <p class="text-sm text-gray-500">Category: {{ $item['category']->name }}</p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">
                                                ${{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                                            <p class="text-sm text-gray-500">${{ number_format($item['price'], 2) }} each
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Coupon Application -->
                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Apply Coupon</h4>
                                <p class="text-xs text-gray-600 mb-3">
                                    <strong>Note:</strong> Coupons only apply when ALL products in your cart belong to the
                                    eligible categories.
                                </p>
                                <div class="flex space-x-2">
                                    <input type="text" id="coupon_code" placeholder="Enter coupon code"
                                        class="flex-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" id="apply_coupon"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                        Apply
                                    </button>
                                </div>
                                <div id="coupon_message" class="mt-2 text-sm"></div>
                            </div>

                            <!-- Checkout Form -->
                            <form action="{{ route('checkout.process') }}" method="POST" class="mt-6 space-y-4">
                                @csrf
                                <input type="hidden" name="coupon_code" id="applied_coupon_code">

                                <div>
                                    <label for="shipping_address" class="block text-sm font-medium text-gray-700">Shipping
                                        Address *</label>
                                    <textarea name="shipping_address" id="shipping_address" rows="3" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter your shipping address"></textarea>
                                </div>

                                <div>
                                    <label for="billing_address" class="block text-sm font-medium text-gray-700">Billing
                                        Address *</label>
                                    <textarea name="billing_address" id="billing_address" rows="3" required
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter your billing address"></textarea>
                                </div>

                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Order
                                        Notes</label>
                                    <textarea name="notes" id="notes" rows="2"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Any special instructions for your order"></textarea>
                                </div>

                                <button type="submit"
                                    class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg">
                                    Place Order
                                </button>
                            </form>
                        </div>

                        <!-- Order Totals -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 p-6 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Totals</h3>

                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Subtotal:</span>
                                        <span class="font-medium">${{ number_format($subtotal, 2) }}</span>
                                    </div>

                                    <div class="flex justify-between" id="discount_row" style="display: none;">
                                        <span class="text-gray-600">Discount:</span>
                                        <span class="font-medium text-green-600" id="discount_amount">-$0.00</span>
                                    </div>

                                    <hr class="my-3">

                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total:</span>
                                        <span id="total_amount">${{ number_format($subtotal, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const couponCodeInput = document.getElementById('coupon_code');
            const applyCouponBtn = document.getElementById('apply_coupon');
            const couponMessage = document.getElementById('coupon_message');
            const appliedCouponCode = document.getElementById('applied_coupon_code');
            const discountRow = document.getElementById('discount_row');
            const discountAmount = document.getElementById('discount_amount');
            const totalAmount = document.getElementById('total_amount');

            const subtotal = {{ $subtotal }};
            const taxRate = 0.1;

            applyCouponBtn.addEventListener('click', function() {
                const code = couponCodeInput.value.trim();
                if (!code) {
                    showMessage('Please enter a coupon code.', 'error');
                    return;
                }

                // Show loading state
                applyCouponBtn.disabled = true;
                applyCouponBtn.textContent = 'Applying...';

                // Validate coupon via AJAX
                fetch('{{ route('validate-coupon') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify({
                            code: code,
                            order_amount: subtotal
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.valid) {
                            showMessage(data.message, 'success');
                            applyCoupon(code, data.coupon);
                        } else {
                            showMessage(data.message, 'error');
                            removeCoupon();
                        }
                    })
                    .catch(error => {
                        showMessage('An error occurred while validating the coupon.', 'error');
                        removeCoupon();
                    })
                    .finally(() => {
                        applyCouponBtn.disabled = false;
                        applyCouponBtn.textContent = 'Apply';
                    });
            });

            function applyCoupon(code, coupon) {
                appliedCouponCode.value = code;

                let discount = 0;
                if (coupon.discount_type === 'percentage') {
                    discount = subtotal * (coupon.discount_value / 100);
                } else {
                    discount = coupon.discount_value;
                }

                // Update display
                discountRow.style.display = 'flex';
                discountAmount.textContent = '-$' + discount.toFixed(2);

                const newTotal = subtotal + (subtotal * taxRate) - discount;
                totalAmount.textContent = '$' + newTotal.toFixed(2);

                // Disable input and button
                couponCodeInput.disabled = true;
                applyCouponBtn.disabled = true;
                applyCouponBtn.textContent = 'Applied';
            }

            function removeCoupon() {
                appliedCouponCode.value = '';
                discountRow.style.display = 'none';
                discountAmount.textContent = '-$0.00';

                const newTotal = subtotal + (subtotal * taxRate);
                totalAmount.textContent = '$' + newTotal.toFixed(2);

                // Re-enable input and button
                couponCodeInput.disabled = false;
                applyCouponBtn.disabled = false;
                applyCouponBtn.textContent = 'Apply';
            }

            function showMessage(message, type) {
                couponMessage.className = 'mt-2 text-sm ' + (type === 'success' ? 'text-green-600' :
                    'text-red-600');
                couponMessage.textContent = message;
            }
        });
    </script>
@endpush
