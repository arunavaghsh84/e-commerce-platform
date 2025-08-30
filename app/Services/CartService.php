<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartService
{
    /**
     * Get the current user ID or session ID for cart identification
     */
    private function getCartIdentifier(): array
    {
        if (Auth::check()) {
            return ['user_id' => Auth::id(), 'session_id' => null];
        }

        $sessionId = session('cart_session_id');
        if (!$sessionId) {
            $sessionId = Str::uuid()->toString();
            session(['cart_session_id' => $sessionId]);
        }

        return ['user_id' => null, 'session_id' => $sessionId];
    }

    public function addItem(int $productId, int $quantity = 1): void
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        $identifier = $this->getCartIdentifier();

        // Check if item already exists in cart
        $existingItem = CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            // Update existing item
            $existingItem->quantity += $quantity;
            $existingItem->updateTotalPrice();
        } else {
            // Create new item
            CartItem::create([
                'user_id' => $identifier['user_id'],
                'session_id' => $identifier['session_id'],
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'total_price' => $product->price * $quantity,
            ]);
        }
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        $identifier = $this->getCartIdentifier();

        $cartItem = CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            if ($quantity <= 0) {
                $cartItem->delete();
            } else {
                $cartItem->quantity = $quantity;
                $cartItem->updateTotalPrice();
            }
        }
    }

    public function removeItem(int $productId): void
    {
        $identifier = $this->getCartIdentifier();

        CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->where('product_id', $productId)
            ->delete();
    }

    public function clear(): void
    {
        $identifier = $this->getCartIdentifier();

        CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])->delete();

        // Clear session cart ID for guest users
        if (!$identifier['user_id']) {
            session()->forget('cart_session_id');
        }
    }

    public function getCart(): array
    {
        $identifier = $this->getCartIdentifier();

        $cartItems = CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->with('product')
            ->get();

        $cart = [];
        foreach ($cartItems as $item) {
            $cart[$item->product_id] = [
                'id' => $item->product_id,
                'name' => $item->product->name,
                'sku' => $item->product->sku,
                'price' => $item->unit_price,
                'category_id' => $item->product->category_id,
                'quantity' => $item->quantity,
            ];
        }

        return $cart;
    }

    public function getCartItems(): Collection
    {
        $identifier = $this->getCartIdentifier();

        return CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'price' => $item->unit_price,
                    'category_id' => $item->product->category_id,
                    'quantity' => $item->quantity,
                ];
            });
    }

    public function getSubtotal(): float
    {
        $identifier = $this->getCartIdentifier();

        return CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->sum('total_price');
    }

    public function getItemCount(): int
    {
        $identifier = $this->getCartIdentifier();

        return CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->sum('quantity');
    }

    public function isEmpty(): bool
    {
        $identifier = $this->getCartIdentifier();

        return !CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])->exists();
    }

    public function getCartWithProducts(): Collection
    {
        $identifier = $this->getCartIdentifier();

        return CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->with(['product.category'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'price' => $item->unit_price,
                    'category_id' => $item->product->category_id,
                    'quantity' => $item->quantity,
                    'product' => $item->product,
                    'category' => $item->product->category,
                ];
            });
    }

    /**
     * Transfer cart items from session to user when they log in
     */
    public function transferSessionCartToUser(int $userId): void
    {
        $sessionId = session('cart_session_id');
        if (!$sessionId) {
            return;
        }

        // Get session cart items
        $sessionItems = CartItem::forSession($sessionId)->get();

        foreach ($sessionItems as $item) {
            // Check if user already has this product in their cart
            $existingItem = CartItem::forUser($userId)
                ->where('product_id', $item->product_id)
                ->first();

            if ($existingItem) {
                // Merge quantities
                $existingItem->quantity += $item->quantity;
                $existingItem->updateTotalPrice();
                $item->delete(); // Remove session item
            } else {
                // Transfer item to user
                $item->update([
                    'user_id' => $userId,
                    'session_id' => null,
                ]);
            }
        }

        // Clear session cart ID
        session()->forget('cart_session_id');
    }

    /**
     * Get cart summary information
     */
    public function getCartSummary(): array
    {
        $identifier = $this->getCartIdentifier();

        $cartItems = CartItem::forUserOrSession($identifier['user_id'], $identifier['session_id'])
            ->with('product')
            ->get();

        $subtotal = $cartItems->sum('total_price');
        $itemCount = $cartItems->sum('quantity');
        $uniqueItems = $cartItems->count();

        return [
            'subtotal' => $subtotal,
            'item_count' => $itemCount,
            'unique_items' => $uniqueItems,
            'total_amount' => $subtotal + round($subtotal * 0.1, 2) + 10.00,
        ];
    }
}
