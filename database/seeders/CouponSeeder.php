<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'ELECTRONICS20',
                'name' => 'Electronics 20% Off',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(3),
                'is_active' => true,
                'categories' => [1], // Electronics
            ],
            [
                'code' => 'CLOTHING15',
                'name' => 'Clothing 15% Off',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(2),
                'is_active' => true,
                'categories' => [2], // Clothing
            ],
            [
                'code' => 'BOOKS10',
                'name' => 'Books $10 Off',
                'discount_type' => 'fixed',
                'discount_value' => 10.00,
                'valid_from' => now(),
                'valid_until' => now()->addMonths(6),
                'is_active' => true,
                'categories' => [3], // Books
            ],
            [
                'code' => 'WELCOME25',
                'name' => 'Welcome 25% Off',
                'discount_type' => 'percentage',
                'discount_value' => 25,
                'valid_from' => now()->subDays(30),
                'valid_until' => now()->addMonths(12),
                'is_active' => true,
                'categories' => [1, 2, 3, 4, 5], // All categories
            ],
            [
                'code' => 'FLASH50',
                'name' => 'Flash Sale 50% Off',
                'discount_type' => 'percentage',
                'discount_value' => 50,
                'valid_from' => now(),
                'valid_until' => now()->addDays(7),
                'is_active' => true,
                'categories' => [1, 2], // Electronics and Clothing
            ],
            [
                'code' => 'CHECKOUT20',
                'name' => 'Checkout 20% Off',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'valid_from' => now()->subDays(7),
                'valid_until' => now()->addMonths(1),
                'is_active' => true,
                'categories' => [1, 2, 3, 4, 5], // All categories
            ],
        ];

        foreach ($coupons as $couponData) {
            $categories = $couponData['categories'];
            unset($couponData['categories']);

            $coupon = Coupon::create($couponData);

            if ($categories) {
                $coupon->categories()->attach(array_filter($categories));
            }
        }
    }
}
