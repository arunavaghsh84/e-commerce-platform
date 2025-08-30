<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CouponService
{
    public function validateCouponForOrder(Coupon $coupon, Collection $cartItems, float $orderAmount): bool
    {
        // Check basic validity (dates, usage limits, active status)
        if (!$coupon->isValid()) {
            return false;
        }

        // Check if cart has eligible products for this coupon
        if (!$this->hasEligibleProducts($coupon, $cartItems)) {
            return false;
        }

        return true;
    }

    public function hasEligibleProducts(Coupon $coupon, Collection $cartItems): bool
    {
        $eligibleCategories = $coupon->categories()->pluck('categories.id')->toArray();

        if (empty($eligibleCategories)) {
            return false;
        }

        // Check that ALL products in the cart belong to eligible categories
        foreach ($cartItems as $item) {
            $product = Product::find($item['id']);
            if (!$product || !$coupon->appliesToProduct($product)) {
                // If any product doesn't match the coupon categories, the coupon is not valid
                return false;
            }
        }

        // All products must match for the coupon to be valid
        return true;
    }

    public function calculateDiscount(Coupon $coupon, float $orderAmount): float
    {
        $discount = $coupon->discount_type === 'percentage'
            ? ($orderAmount * $coupon->discount_value / 100)
            : $coupon->discount_value;

        return round($discount, 2);
    }

    public function getValidCoupons(): Collection
    {
        return Coupon::valid()->get();
    }

    public function validateCouponForCart(Coupon $coupon, Collection $cartItems): array
    {
        $result = [
            'valid' => false,
            'message' => '',
            'errors' => [],
            'coupon' => null
        ];

        // Check if coupon exists and is active
        if (!$coupon) {
            $result['message'] = 'Coupon not found.';
            return $result;
        }

        // Check if coupon is active
        if (!$coupon->is_active) {
            $result['message'] = 'This coupon is inactive.';
            return $result;
        }

        // Check date validity
        $now = Carbon::now();
        if ($now->lt($coupon->valid_from)) {
            $result['message'] = 'This coupon is not yet active. Valid from: ' . $coupon->valid_from->format('M d, Y');
            return $result;
        }

        if ($now->gt($coupon->valid_until)) {
            $result['message'] = 'This coupon has expired. Expired on: ' . $coupon->valid_until->format('M d, Y');
            return $result;
        }

        // Skip usage limit checks as these fields don't exist in current schema

        // Check if cart has eligible products
        if (!$this->hasEligibleProducts($coupon, $cartItems)) {
            $eligibleCategories = $coupon->categories()->pluck('categories.name')->toArray();
            $result['message'] = 'This coupon is not applicable to all products in your cart. ALL products must belong to these categories: ' . implode(', ', $eligibleCategories);
            return $result;
        }

        // Skip minimum order amount check as this field doesn't exist in current schema

        // All validations passed
        $result['valid'] = true;
        $result['message'] = 'Coupon applied successfully!';
        $result['coupon'] = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $coupon->discount_value,
            'valid_from' => $coupon->valid_from->format('M d, Y'),
            'valid_until' => $coupon->valid_until->format('M d, Y'),
        ];

        return $result;
    }

    public function getApplicableCoupons(Collection $cartItems): Collection
    {
        $validCoupons = $this->getValidCoupons();
        $applicableCoupons = collect();

        foreach ($validCoupons as $coupon) {
            if ($this->hasEligibleProducts($coupon, $cartItems)) {
                $applicableCoupons->push($coupon);
            }
        }

        return $applicableCoupons;
    }
}
