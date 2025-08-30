<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function index()
    {
        $cartItems = $this->cartService->getCartWithProducts();
        $subtotal = $this->cartService->getSubtotal();
        $itemCount = $this->cartService->getItemCount();

        return view('cart.index', compact('cartItems', 'subtotal', 'itemCount'));
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $this->cartService->addItem($request->product_id, $request->quantity);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $this->cartService->getItemCount(),
            'cart_subtotal' => $this->cartService->getSubtotal(),
        ]);
    }

    public function updateQuantity(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:0|max:100',
        ]);

        $this->cartService->updateQuantity($request->product_id, $request->quantity);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'cart_count' => $this->cartService->getItemCount(),
            'cart_subtotal' => $this->cartService->getSubtotal(),
        ]);
    }

    public function removeItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);

        $this->cartService->removeItem($request->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully!',
            'cart_count' => $this->cartService->getItemCount(),
            'cart_subtotal' => $this->cartService->getSubtotal(),
        ]);
    }

    public function clear(): JsonResponse
    {
        $this->cartService->clear();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully!',
            'cart_count' => 0,
            'cart_subtotal' => 0,
        ]);
    }

    public function getCartInfo(): JsonResponse
    {
        return response()->json([
            'cart_count' => $this->cartService->getItemCount(),
            'cart_subtotal' => $this->cartService->getSubtotal(),
            'is_empty' => $this->cartService->isEmpty(),
        ]);
    }
}
