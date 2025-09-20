<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Vendor\Product;
use App\Models\Vendor\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Display the active cart with its items.
     */
    public function index()
    {
        $cart = $this->getOrCreateActiveCart();

        return view('orders.cart', [
            'cart' => $cart,
            'items' => $cart->items()->with('product')->get(),
        ]);
    }

    /**
     * Add a product to the cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->getOrCreateActiveCart();

        $existingItem = $cart->items()->where('product_id', $request->product_id)->first();

        if ($existingItem) {
            $existingItem->update(['quantity' => $request->quantity]);
        } else {
            $cart->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }


        return redirect()->route('cart.index')->with('success', 'Product added to cart.');
    }

    /**
     * Show the specified cart (if needed).
     */
    public function show(Cart $cart)
    {
        abort_unless($cart->user_id === Auth::id(), 403);

        return view('cart.show', [
            'cart' => $cart->load('items.product'),
        ]);
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        abort_unless($cartItem->cart_id === auth()->user()?->activeCart?->id, 403);

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated.');
    }

    /**
     * Remove a product from the cart.
     */
    public function destroy(Request $request, Cart $cart)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        abort_unless($cart->user_id === Auth::id(), 403);

        $cart->items()->where('product_id', $request->product_id)->delete();

        return back()->with('success', 'Product removed from cart.');
    }

    public function destroyItem(\App\Models\CartItem $cartItem)
    {
        $activeCart = auth()->user()?->activeCart;

        abort_unless($cartItem->cart_id === $activeCart?->id, 403);

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    /**
     * Get or create the authenticated user's active cart.
     */
    protected function getOrCreateActiveCart(): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => Auth::id(),
            'status' => 'active',
        ]);
    }


    public function bulkAdd(Request $request)
    {
        // Step 1: Validate structure
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variants' => ['required', 'array'],
            'variants.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $product = Product::findOrFail($request->product_id);

        // Step 2: Custom validation against variant stock
        $errors = [];

        foreach ($request->variants as $variantId => $qty) {
            if ((int) $qty <= 0) {
                continue;
            }

            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $product->id)
                ->first();

            if (!$variant) {
                $errors["variants.$variantId"] = 'Invalid variant selected.';
                continue;
            }

            if ($qty > $variant->stock) {
                $errors["variants.$variantId"] = "Only {$variant->stock} in stock.";
            }
        }

        // Step 3: If validation fails, flash errors and redirect
        if (!empty($errors)) {
            return redirect()
                ->back()
                ->withErrors($errors)
                ->withInput()
                ->with('error', 'Some variants have issues. Please fix them and try again.');
        }

        // Step 4: Proceed with cart update
        $cart = $this->getOrCreateActiveCart();

        foreach ($request->variants as $variantId => $qty) {
            if ((int) $qty <= 0) {
                continue;
            }

            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $product->id)
                ->first();

            if (!$variant) continue;

            $existing = $cart->items()
                ->where('product_variant_id', $variant->id)
                ->first();

            if ($existing) {
                $existing->update(['quantity' => $qty]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $qty,
                ]);
            }
        }

        return redirect()->route('cart.index')->with('success', 'Variants added to cart.');
    }


}
