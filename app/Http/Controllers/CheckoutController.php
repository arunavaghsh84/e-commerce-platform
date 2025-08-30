<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;

class CheckoutController extends Controller
{
    protected CartService $cartService;
    protected CouponService $couponService;

    public function __construct(CartService $cartService, CouponService $couponService)
    {
        $this->cartService = $cartService;
        $this->couponService = $couponService;
    }

    public function index()
    {
        // Use CartService to get cart items
        $cartItems = $this->cartService->getCartWithProducts();
        $subtotal = $this->cartService->getSubtotal();

        // If cart is empty, redirect to products page or show empty cart message
        if ($this->cartService->isEmpty()) {
            return redirect()->route('admin.products.index')
                ->with('info', 'Your cart is empty. Please add some products first.');
        }

        return view('checkout.index', compact('cartItems', 'subtotal'));
    }

    public function process(Request $request): RedirectResponse
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'billing_address' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        // Get cart items from CartService
        $cartItems = $this->cartService->getCartWithProducts();
        $subtotal = $this->cartService->getSubtotal();

        $discountAmount = 0;
        $couponCode = null;
        $couponId = null;

        // Validate coupon if provided
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)->first();

            if (!$coupon || !$this->couponService->validateCouponForOrder($coupon, $cartItems, $subtotal)) {
                return back()->with('error', 'Invalid or expired coupon code.');
            }

            // Coupon is valid, calculate discount
            $discountAmount = $this->couponService->calculateDiscount($coupon, $subtotal);
            $couponCode = $coupon->code;
            $couponId = $coupon->id;
        }

        $totalAmount = $subtotal - $discountAmount;

        try {
            DB::beginTransaction();

            // Create order items from cart
            foreach ($cartItems as $item) {
                // Ensure the item has all required fields
                if (!isset($item['id']) || !isset($item['name']) || !isset($item['price']) || !isset($item['quantity'])) {
                    Log::warning('Invalid cart item structure: ' . json_encode($item));
                    continue;
                }
            }

            DB::commit();

            // Clear the cart after successful order
            $this->cartService->clear();

            return redirect()->route('checkout.success')
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order processing failed: ' . $e->getMessage());

            return back()->with('error', 'An error occurred while processing your order. Please try again.');
        }
    }

    public function success()
    {
        return view('checkout.success');
    }
}
