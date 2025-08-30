<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(): View
    {
        $coupons = Coupon::with('categories')->latest()->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create(): View
    {
        $categories = Category::active()->get();

        return view('admin.coupons.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $coupon = Coupon::create($validated);

        if (isset($validated['category_ids'])) {
            $coupon->categories()->attach($validated['category_ids']);
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function show(Coupon $coupon): View
    {
        $coupon->load('categories');

        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon): View
    {
        $categories = Category::active()->get();
        $coupon->load('categories');

        return view('admin.coupons.edit', compact('coupon', 'categories'));
    }

    public function update(Request $request, Coupon $coupon): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
            'category_ids' => 'required|array|min:1',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $coupon->update($validated);

        if (isset($validated['category_ids'])) {
            $coupon->categories()->sync($validated['category_ids']);
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        if ($coupon->used_count > 0) {
            return back()->with('error', 'Cannot delete coupon that has been used.');
        }

        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    public function toggleStatus(Coupon $coupon): RedirectResponse
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $status = $coupon->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.coupons.index')
            ->with('success', "Coupon {$status} successfully.");
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid coupon code.',
            ]);
        }

        // Get cart items from session using CartService
        $cartService = app(\App\Services\CartService::class);
        $cartItems = $cartService->getCartWithProducts();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'valid' => false,
                'message' => 'Your cart is empty. Please add some products first.',
            ]);
        }

        // Use the enhanced CouponService for validation
        $couponService = app(\App\Services\CouponService::class);
        $validationResult = $couponService->validateCouponForCart($coupon, $cartItems);

        return response()->json($validationResult);
    }
}
