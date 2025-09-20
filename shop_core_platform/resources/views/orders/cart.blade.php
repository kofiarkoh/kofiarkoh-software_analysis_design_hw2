<x-layout>
    <x-slot:title>
        Cart
    </x-slot>
    <div class="breadcrumb-block style-shared">
        <div class="breadcrumb-main bg-linear overflow-hidden">
            <div class="container lg:pt-[134px] pt-24 pb-10 relative">
                <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                    <div class="text-content">
                        <div class="heading2 text-center">Shopping Cart</div>
                        <div class="link flex items-center justify-center gap-1 caption1 mt-3">
                            <a href="index.html">Homepage</a>
                            <i class="ph ph-caret-right text-sm text-secondary2"></i>
                            <div class="text-secondary2 capitalize">Shopping Cart</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="cart-block md:py-20 py-10">
        <div class="container">
            <div class="content-main flex justify-between max-xl:flex-col gap-y-8">
                <div class="{{ $items->isNotEmpty() ? 'xl:w-2/3 xl:pr-3' : '' }} w-full">
                    {{-- Table headers for desktop --}}
                    <div class="hidden md:grid grid-cols-12 gap-4 font-semibold text-sm border-b pb-2 text-gray-700">
                        <div class="col-span-4">Product</div>
                        <div class="col-span-2 text-center">Price</div>
                        <div class="col-span-2 text-center">Quantity</div>
                        <div class="col-span-2 text-center">Total</div>
                        <div class="col-span-2 text-center">Action</div>
                    </div>

                    <div class="space-y-4 mt-4">
                        <div class="space-y-4 mt-4">
                            @forelse ($items as $item)
                                @php
                                    $price = $item->variant->price ?? $item->product->price ?? 0;
                                @endphp

                                <div class="border rounded-md p-4 flex flex-col justify-between gap-4 mt-4">
                                    {{-- Product Info + Price --}}
                                    <div class="md:grid md:grid-cols-6 md:gap-4">
                                        {{-- Product Info --}}
                                        <div class="md:col-span-4 flex gap-4">
                                                <img src="{{ Storage::disk('public')->url($item->product->photos[0]) }}"
                                                     alt="{{ $item->product->name }}"
                                                     class="w-20 h-20 object-cover rounded">
                                            <div>
                                                <a href="{{ route('products.detail', $item->product) }}"
                                                   class="font-medium text-blue-600 hover:underline">
                                                    {{ $item->product->name }}
                                                </a>
                                                @if ($item->variant)
                                                    <div class="text-sm text-gray-500 mt-1">
                                                        {{ $item->variant->sku }}
                                                    </div>
                                                @endif
                                                @if ($item->variant && $item->variant->attributeValues->isNotEmpty())
                                                    <div class="text-sm text-gray-500 mt-1">
                                                        @foreach ($item->variant->attributeValues as $val)
                                                            {{ $val->attribute->name }}: {{ $val->value }}{{ !$loop->last ? ',' : '' }}
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Price --}}
                                        <div class="md:col-span-2 mt-4 md:mt-0 text-left md:text-right">
                                            <div class="text-sm text-gray-500">Price</div>
                                            <div class="font-medium">GH₵ {{ number_format($price, 2) }}</div>
                                        </div>
                                    </div>

                                    {{-- Bottom Actions: Quantity + Remove --}}
                                    <div class="flex justify-between items-center mt-2">

                                        @if($item->product_stock < $item->quantity)
                                                <span class="text-sm text-red-600 font-semibold">Out of Stock ( {{$item->product_stock }} items left )</span>
                                            @else
                                            {{-- Quantity Form --}}
                                            <form method="POST" action="{{ route('cart.update', $item) }}" class="cart-qty-form">
                                                @csrf
                                                @method('PUT')
                                                <div class="cart-qty-control flex items-center border border-line rounded-lg px-2 py-1 w-fit">
                                                    <button type="button" class="decrease-cart-qty px-2">−</button>
                                                    <div class="cart-qty-display font-semibold text-center w-6">{{ $item->quantity }}</div>
                                                    <input type="hidden" name="quantity" class="cart-qty-input" value="{{ $item->quantity }}">
                                                    <button type="button" class="increase-cart-qty px-2">+</button>
                                                </div>
                                            </form>

                                        @endif


                                        {{-- Remove Button --}}
                                        <form action="{{ route('cart.item.destroy', $item) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red hover:underline text-sm">Remove</button>
                                        </form>
                                    </div>
                                </div>

                            @empty
                                <div class="flex items-center justify-center w-full">
                                    <div class="text-center">
                                        <img src="{{ asset('assets/images/empty-cart.png') }}" alt="Empty Cart" class="w-32 h-32 mx-auto mb-4">
                                        <p class="text-lg text-gray-600 font-semibold mt-4">Your cart is currently empty.</p>
                                        <a href="/" class="mt-4 inline-block bg-black text-white px-6 py-2 rounded hover:bg-gray-800 transition">
                                            Continue Shopping
                                        </a>
                                    </div>
                                </div>

                            @endforelse
                        </div>

                    </div>
                </div>

                @if ($items->isNotEmpty())
                <div class="xl:w-1/3 xl:pl-12 w-full">

                    <div class="checkout-block bg-surface p-6 rounded-2xl">
                        <div class="heading5">Order Summary</div>
                        <div class="total-block py-5 flex justify-between border-b border-line">
                            <div class="text-title">Subtotal</div>
                            <div class="text-title">GH₵<span class="total-product">{{ auth()->user()?->cartTotalPrice() ?? '0.00' }}</span></div>
                        </div>
                        <div class="total-cart-block pt-4 pb-4 flex justify-between">
                            <div class="heading5">Total</div>
                            <div class=""><span class="heading5">GH₵</span><span class="total-cart heading5">{{ auth()->user()?->cartTotalPrice() ?? '0.00' }}</span></div>
                        </div>
                        <div class="block-button flex flex-col items-center gap-y-4 mt-5">
                            <a href="{{ route('checkout.address') }}" class="checkout-btn button-main text-center w-full"> Proceed To Checkout</a>
                            <a class="text-button hover-underline" href="/shop-breadcrumb1.html">Continue shopping </a>
                        </div>
                    </div>

                </div>
                @endif
            </div>
        </div>
    </div>


    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.cart-qty-form').forEach(form => {
                    const minus = form.querySelector('.decrease-cart-qty');
                    const plus = form.querySelector('.increase-cart-qty');
                    const display = form.querySelector('.cart-qty-display');
                    const input = form.querySelector('.cart-qty-input');

                    const update = (delta) => {
                        let current = parseInt(display.textContent);
                        const newQty = Math.max(1, current + delta);
                        if (newQty !== current) {
                            display.textContent = newQty;
                            input.value = newQty;
                            form.submit(); // auto-submit on change
                        }
                    };

                    minus.addEventListener('click', e => {
                        e.preventDefault();
                        update(-1);
                    });

                    plus.addEventListener('click', e => {
                        e.preventDefault();
                        update(1);
                    });
                });
            });
        </script>

    @endsection
</x-layout>
