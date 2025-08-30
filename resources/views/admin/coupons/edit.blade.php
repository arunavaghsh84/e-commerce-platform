@extends('layouts.app')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Edit Coupon</h2>
                        <a href="{{ route('admin.coupons.index') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Back to Coupons
                        </a>
                    </div>

                    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Coupon Code -->
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700">Coupon Code *</label>
                                <input type="text" name="code" id="code" value="{{ old('code', $coupon->code) }}"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 font-mono">
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Coupon Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Coupon Name *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $coupon->name) }}"
                                    required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Discount Type -->
                            <div>
                                <label for="discount_type" class="block text-sm font-medium text-gray-700">Discount Type
                                    *</label>
                                <select name="discount_type" id="discount_type" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select discount type</option>
                                    <option value="percentage"
                                        {{ old('discount_type', $coupon->discount_type) === 'percentage' ? 'selected' : '' }}>
                                        Percentage</option>
                                    <option value="fixed"
                                        {{ old('discount_type', $coupon->discount_type) === 'fixed' ? 'selected' : '' }}>
                                        Fixed Amount</option>
                                </select>
                                @error('discount_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Discount Value -->
                            <div>
                                <label for="discount_value" class="block text-sm font-medium text-gray-700">Discount Value
                                    *</label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm"
                                            id="discount-symbol">{{ $coupon->discount_type === 'percentage' ? '%' : '$' }}</span>
                                    </div>
                                    <input type="number" name="discount_value" id="discount_value"
                                        value="{{ old('discount_value', $coupon->discount_value) }}" step="0.01"
                                        min="0" required
                                        class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                @error('discount_value')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Valid From -->
                            <div>
                                <label for="valid_from" class="block text-sm font-medium text-gray-700">Valid From *</label>
                                <input type="datetime-local" name="valid_from" id="valid_from"
                                    value="{{ old('valid_from', $coupon->valid_from->format('Y-m-d\TH:i')) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('valid_from')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Valid Until -->
                            <div>
                                <label for="valid_until" class="block text-sm font-medium text-gray-700">Valid Until
                                    *</label>
                                <input type="datetime-local" name="valid_until" id="valid_until"
                                    value="{{ old('valid_until', $coupon->valid_until->format('Y-m-d\TH:i')) }}" required
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                @error('valid_until')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Applicable Categories -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Applicable Categories *</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach ($categories as $category)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"
                                            {{ in_array($category->id, old('category_ids', $coupon->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('category_ids')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.coupons.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Coupon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('discount_type').addEventListener('change', function() {
            const symbol = this.value === 'percentage' ? '%' : '$';
            document.getElementById('discount-symbol').textContent = symbol;
        });
    </script>
@endsection
