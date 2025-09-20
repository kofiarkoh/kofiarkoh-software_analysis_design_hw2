<x-layout>
    <x-slot:title>
        Products
    </x-slot>

    <div class="shop-product lg:py-20 md:py-20 py-16">
        <div class="container">
            <div
                class="list grid xl:grid-cols-5 lg:grid-cols-4 md:grid-cols-3 grid-cols-2 sm:gap-[30px] gap-[20px] md:mt-10 mt-6">
                @foreach ($products as $product)
                    @include('partials.product-card', [
                                 '$product' => $product,
                             ])
{{--                    <a href="{{ isset($shop) ? route('shops.products.show', ['shop' => $shop->id, 'product' => $product->id]) : route('products.detail', ['product' => $product->id]) }}">--}}
{{--                        <div class="product-item style-marketplace p-4 border border-line rounded-2xl" data-item="152">--}}
{{--                            <div class="bg-img relative w-full aspect-1/1">--}}
{{--                                <img class="product-image" src="{{ Storage::disk('public')->url($product->photos[0]) }}" alt=""/>--}}
{{--                            </div>--}}
{{--                            <div class="product-infor mt-4">--}}
{{--                                <span class="text-title">{{$product->name}}</span>--}}
{{--                                <div class="flex gap-0.5 mt-1">--}}
{{--                                    <i class="ph-fill ph-star text-sm text-yellow"></i>--}}
{{--                                    <i class="ph-fill ph-star text-sm text-yellow"></i>--}}
{{--                                    <i class="ph-fill ph-star text-sm text-yellow"></i>--}}
{{--                                    <i class="ph-fill ph-star text-sm text-yellow"></i>--}}
{{--                                    <i class="ph-fill ph-star text-sm text-yellow"></i>--}}
{{--                                </div>--}}
{{--                                <span class="text-title inline-block mt-1">GH₵ {{ $product->price }}</span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </a>--}}
                @endforeach

            </div>

        </div>
    </div>

    <div class="p-4">
        {{ $products->links() }}
    </div>
</x-layout>
