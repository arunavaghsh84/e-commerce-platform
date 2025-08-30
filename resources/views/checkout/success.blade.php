@extends('layouts.app')

@section('title', 'Order Confirmation')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    <div class="mb-6">
                        <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Order Confirmed!</h2>
                    <p class="text-lg text-gray-600 mb-6">Thank you for your order. Your order has been successfully placed.
                    </p>

                    <div class="bg-gray-50 p-6 rounded-lg max-w-md mx-auto mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Order Details</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Number:</span>
                                <span class="font-medium">#O1234</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-medium capitalize">Created</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Amount:</span>
                                <span class="font-medium">${{ number_format(123.45, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Coupon Applied:</span>
                                <span class="font-medium text-green-600">ELECTRONICS20</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Discount:</span>
                                <span class="font-medium text-green-600">-${{ number_format(12.34, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-x-4">
                        <a href="{{ route('admin.coupons.index') }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Back to Admin
                        </a>
                        <a href="{{ route('checkout.index') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            New Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
