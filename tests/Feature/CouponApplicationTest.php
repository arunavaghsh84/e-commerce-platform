<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use App\Services\CouponService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CouponApplicationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_apply_valid_coupon_to_eligible_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
        ]);
        $coupon = Coupon::factory()->create([
            'code' => 'SAVE20',
            'discount_type' => 'percentage',
            'discount_value' => 20,
        ]);
        $coupon->categories()->attach($category->id);

        $couponService = new CouponService();
        
        $cartItems = collect([
            ['id' => $product->id, 'price' => 100.00, 'quantity' => 1]
        ]);

        // Test coupon validation
        $result = $couponService->validateCouponForCart($coupon, $cartItems);
        $this->assertTrue($result['valid']);
        $this->assertEquals('SAVE20', $result['coupon']['code']);

        // Test discount calculation
        $discount = $couponService->calculateDiscount($coupon, 100.00);
        $this->assertEquals(20.00, $discount);
    }

    /** @test */
    public function it_rejects_expired_coupon(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $expiredCoupon = Coupon::factory()->expired()->create();
        $expiredCoupon->categories()->attach($category->id);

        $couponService = new CouponService();
        
        $cartItems = collect([
            ['id' => $product->id, 'price' => 100.00, 'quantity' => 1]
        ]);

        $result = $couponService->validateCouponForCart($expiredCoupon, $cartItems);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('expired', $result['message']);
    }

    /** @test */
    public function it_rejects_coupon_for_wrong_category(): void
    {
        $electronicsCategory = Category::factory()->create(['name' => 'Electronics']);
        $booksCategory = Category::factory()->create(['name' => 'Books']);
        
        $book = Product::factory()->create(['category_id' => $booksCategory->id]);
        $electronicsCoupon = Coupon::factory()->create(['code' => 'ELECTRONICS20']);
        $electronicsCoupon->categories()->attach($electronicsCategory->id);

        $couponService = new CouponService();
        
        $cartItems = collect([
            ['id' => $book->id, 'price' => 50.00, 'quantity' => 1]
        ]);

        $result = $couponService->validateCouponForCart($electronicsCoupon, $cartItems);

        $this->assertFalse($result['valid']);
        $this->assertStringContainsString('not applicable', $result['message']);
    }
}