<?php

namespace Tests\Unit;

use App\Models\Coupon;
use App\Models\Category;
use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_determine_if_coupon_is_valid(): void
    {
        $validCoupon = Coupon::factory()->create([
            'is_active' => true,
            'valid_from' => now()->subDays(1),
            'valid_until' => now()->addDays(1),
        ]);

        $this->assertTrue($validCoupon->isValid());
    }

    /** @test */
    public function it_returns_false_when_coupon_is_expired(): void
    {
        $expiredCoupon = Coupon::factory()->expired()->create();

        $this->assertFalse($expiredCoupon->isValid());
        $this->assertTrue($expiredCoupon->isExpired());
    }

    /** @test */
    public function it_returns_false_when_coupon_is_inactive(): void
    {
        $inactiveCoupon = Coupon::factory()->inactive()->create();

        $this->assertFalse($inactiveCoupon->isValid());
    }

    /** @test */
    public function it_can_apply_to_category_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        $coupon = Coupon::factory()->create();
        
        $coupon->categories()->attach($category->id);

        $this->assertTrue($coupon->appliesToCategory($category->id));
        $this->assertTrue($coupon->appliesToProduct($product));
    }

    /** @test */
    public function it_has_relationship_with_categories(): void
    {
        $coupon = Coupon::factory()->create();
        $categories = Category::factory(2)->create();

        $coupon->categories()->attach($categories->pluck('id'));

        $this->assertCount(2, $coupon->categories);
        $this->assertInstanceOf(Category::class, $coupon->categories->first());
    }
}