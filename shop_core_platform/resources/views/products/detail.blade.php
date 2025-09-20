@php
    $hasVariants = $product->variants->isNotEmpty();
     $quantity = $cartItem?->quantity ?? 1;
@endphp

<x-layout>
    <x-slot:title>
        Product
    </x-slot>

    @section('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

        <!-- Fancybox CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css"/>

        <!-- Fancybox CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css"/>

        <style>

            .product-slider {
                max-width: 450px;
                margin: auto;
            }

            .slider-for img,
            .slider-nav img {
                width: 100%;
                border-radius: 8px;
                cursor: pointer;
            }

            .slider-for img {
                height: 400px;
                object-fit: contain;
            }

            .slider-nav img {
                height: 70px;
                object-fit: contain;
            }

            .slider-nav {
                margin-top: 10px;
            }

            .slider-nav .slick-slide {
                margin: 0 5px;
            }

            .slider-nav .slick-current img {
                border: 2px solid #007bff;
            }

            /* Custom modal size */
            .fancybox-custom-size {
                max-width: 700px;
                width: 90%;
                margin: auto;
                border-radius: 10px;
                overflow: hidden;
            }
        </style>
    @endsection
    <div class="container mt-8 pb-12">
        <div class="product-detail">
            <div class="product-content md:pt-10 pt-7">
                <div class="flex flex-wrap justify-between gap-y-8">

                    <!-- Product Gallery -->
                    <div class="product-gallery md:w-1/2 w-full md:pr-12">
                        <div class="product-slider">
                            <div class="slider-for">
                                @foreach ($photoUrls as $url)
                                    <div>
                                        <a data-fancybox="gallery" href="{{ $url }}">
                                            <img src="{{ $url }}" alt="Product Image" class="w-full rounded-lg shadow-sm"/>
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            <div class="slider-nav mt-4">
                                @foreach ($photoUrls as $url)
                                    <div>
                                        <img src="{{ $url }}" alt="Thumbnail" class="w-20 h-20 object-cover rounded-md"/>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Product Information -->
                    <div class="product-info md:w-1/2 w-full md:pl-4 lg:pl-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="product-category text-sm text-gray-500 uppercase font-semibold">Fashion</div>
                                <h1 class="product-name text-2xl font-bold mt-2">{{ $product->name }}</h1>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="flex items-center gap-2 mt-3">
                            <div class="flex text-yellow-500">
                                <i class="ph-fill ph-star text-sm"></i>
                                <i class="ph-fill ph-star text-sm"></i>
                                <i class="ph-fill ph-star text-sm"></i>
                                <i class="ph-fill ph-star text-sm"></i>
                                <i class="ph-fill ph-star text-sm"></i>
                            </div>
                            <a class="" href="{{ $product->shop->getWebsiteURL() }}" target="_blank"><span class="text-sm text-gray-600 underline">Sold by {{$product->shop->name}}</span></a>
                        </div>

                        <!-- Price & Description -->
                        <div class="mt-5 pb-6 border-gray-200">
                            @if(! $hasVariants)
                                <div class="product-price text-2xl font-semibold text-black">GH₵ {{ $product->price }}</div>
                            @endif
                        </div>

                        <!-- Quantity & Cart Actions -->
                        <div class="list-action">
                            <div class="text-base font-medium mb-3">Quantity:</div>

                            @if ($hasVariants)
                                {{-- ✅ Bulk Add Multiple Variants --}}
                                @php
                                    $isUpdatingCart = false;
                                    foreach ($product->variants as $variant) {
                                        if (($variantQuantities[$variant->id]->quantity ?? 0) > 0) {
                                            $isUpdatingCart = true;
                                            break;
                                        }
                                    }
                                @endphp

                                <form method="POST" action="{{ route('cart.bulkAdd') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <div class="variant-selection space-y-4 mt-4">
                                        <label class="block text-sm font-medium text-gray-800">Select Quantities by Variant:</label>

                                        @foreach ($product->variants->sortByDesc('stock') as $variant)
                                            <div class="flex items-center justify-between border p-3 rounded-md mt-2 p-2">
                                                <div class="flex-1">
                                                    <div class="font-semibold">{{ $variant->sku }}</div>
                                                    <div class="text-sm text-gray-500">
                                                        @foreach ($variant->attributeValues as $val)
                                                            {{ $val->attribute->name }}: {{ $val->value }}{{ !$loop->last ? ',' : '' }}
                                                        @endforeach
                                                    </div>
                                                    <div class="text-sm font-medium mt-1">
                                                        GH₵ {{ number_format($variant->price, 2) }}
                                                    </div>
                                                </div>

                                                @php
                                                    $inCart = $variantQuantities[$variant->id]->quantity ?? null;
                                                    $defaultQty = $inCart !== null ? $inCart : ($loop->first ? 1 : 0);
                                                @endphp
                                                <x-quantity-selector
                                                    name="variants[{{ $variant->id }}]"
                                                    :max="$variant->stock"
                                                    :value="$defaultQty" />
                                            </div>
                                        @endforeach
                                    </div>

                                    @if ($product->variants->filter(fn ($variant) => $variant->stock > 0)->isNotEmpty())
                                        <div class="mt-6">
                                            <button type="submit" class="button-main w-full bg-black text-white py-3 rounded-md">
                                                {{ $isUpdatingCart ? 'Update Cart' : 'Add to Cart' }}
                                            </button>
                                        </div>
                                    @endif
                                </form>
                            @else
                                {{-- ✅ Single Product Add --}}
                                <form method="POST" action="{{ route('cart.store') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <div class="flex items-center gap-5 mt-6">
                                        <x-quantity-selector name="quantity" :max="$product->quantity" :value="$quantity" />

                                        @if ($product->quantity >= 1)
                                            <button type="submit" class="button-main w-full bg-black text-white py-3 rounded-md">
                                                {{ $cartItem ? 'Update Cart' : 'Add to Cart' }}
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            @endif
                        </div>
                        <div class="mt-5 pb-6 border-t border-gray-200">
                            <h5 class="font-bold mt-2">About This Item</h5>
                            <p class="product-description text-gray-700 mt-2 leading-relaxed">
                                {!! str($product->description)->sanitizeHtml() !!}
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>



    @section('scripts')

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Slick JS -->
        <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

        <!-- Fancybox JS -->
        <!-- Fancybox JS -->
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>

        <!-- Initialize Sliders & Fancybox -->
        <script>
            $(document).ready(function () {
                $('.slider-for').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: false,
                    fade: true,
                    asNavFor: '.slider-nav'
                });

                $('.slider-nav').slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    asNavFor: '.slider-for',
                    dots: false,
                    centerMode: false,
                    focusOnSelect: true
                });
            });

            // Fancybox configuration with custom size
            Fancybox.bind("[data-fancybox='gallery']", {
                Toolbar: {
                    display: ["prev", "next", "close"]
                },
                contentClass: "fancybox-custom-size"
            });
        </script>

    @endsection
</x-layout>
