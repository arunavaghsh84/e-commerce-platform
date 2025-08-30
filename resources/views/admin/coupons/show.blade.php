@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Coupon Details</h2>
                        <div class="space-x-2">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Edit Coupon
                            </a>
                            <a href="{{ route('admin.coupons.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Back to Coupons
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Coupon Information</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Code</dt>
                                    <dd class="text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">
                                        {{ $coupon->code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $coupon->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Discount Type</dt>
                                    <dd class="text-sm text-gray-900 capitalize">{{ $coupon->discount_type }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Discount Value</dt>
                                    <dd class="text-sm text-gray-900">
                                        @if ($coupon->discount_type === 'percentage')
                                            {{ $coupon->discount_value }}%
                                        @else
                                            ${{ number_format($coupon->discount_value, 2) }}
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Valid Period</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $coupon->valid_from->format('M d, Y H:i') }} -
                                        {{ $coupon->valid_until->format('M d, Y H:i') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="text-sm text-gray-900">{{ $coupon->created_at->format('M d, Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                    <dd class="text-sm text-gray-900">{{ $coupon->updated_at->format('M d, Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Applicable Categories</h3>
                            @if ($coupon->categories->count() > 0)
                                <div class="space-y-2">
                                    @foreach ($coupon->categories as $category)
                                        <div class="border border-gray-200 rounded-lg p-3">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $category->name }}</h4>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No categories assigned to this coupon.</p>
                            @endif

                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Validation Status</h3>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Is Valid:</span>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $coupon->isValid() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $coupon->isValid() ? 'Yes' : 'No' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Is Expired:</span>
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $coupon->isExpired() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $coupon->isExpired() ? 'Yes' : 'No' }}
                                        </span>
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
