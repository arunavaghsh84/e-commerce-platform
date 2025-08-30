<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use App\Services\CouponService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CouponServiceTest extends TestCase
{
    use RefreshDatabase;

    private CouponService $couponService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->couponService = new CouponService();
    }

    /** @test */
    public function it_calculates_percentage_discount_correctly(): void
    {
        $coupon = Coupon::factory()->create([
            'discount_type' => 'percentage',
            'discount_value' => 20,
        ]);

        $discount = $this->couponService->calculateDiscount($coupon, 100.00);

        $this->assertEquals(20.00, $discount);
    }

    /** @test */
    public function it_calculates_fixed_discount_correctly(): void
    {
        $coupon = Coupon::factory()->create([
            'discount_type' => 'fixed',
            'discount_value' => 15.00,
        ]);

        $discount = $this->couponService->calculateDiscount($coupon, 100.00);

        $this->assertEquals(15.00, $discount);
    }

    /** @test */
    public function it_validates_coupon_eligibility_for_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $coupon = Coupon::factory()->create();
        $coupon->categories()->attach($category->id);

        $cartItems = collect([
            ['id' => $product->id, 'price' => 100.00, 'quantity' => 1]
        ]);

        $isValid = $this->couponService->validateCouponForOrder($coupon, $cartItems, 100.00);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function it_rejects_coupon_for_wrong_category(): void
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category1->id]);
        $coupon = Coupon::factory()->create();
        $coupon->categories()->attach($category2->id); // Different category

        $cartItems = collect([
            ['id' => $product->id, 'price' => 100.00, 'quantity' => 1]
        ]);

        $hasEligible = $this->couponService->hasEligibleProducts($coupon, $cartItems);

        $this->assertFalse($hasEligible);
    }

    /** @test */
    public function it_validates_coupon_for_cart_with_detailed_result(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $coupon = Coupon::factory()->create([
            'code' => 'TEST20',
            'name' => 'Test Coupon',
        ]);
        $coupon->categories()->attach($category->id);

        $cartItems = collect([
            ['id' => $product->id, 'price' => 100.00, 'quantity' => 1]
        ]);

        $result = $this->couponService->validateCouponForCart($coupon, $cartItems);

        $this->assertTrue($result['valid']);
        $this->assertEquals('Coupon applied successfully!', $result['message']);
        $this->assertEquals('TEST20', $result['coupon']['code']);
    }
}