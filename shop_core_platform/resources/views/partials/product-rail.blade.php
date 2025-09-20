<div class="md:pt-[60px] pt-10">
    <div class="container">
        <div class="heading flex items-center justify-between gap-5 flex-wrap">
            <div class="left flex items-center gap-6 gap-y-3 flex-wrap">
                <div class="heading3">{{ $title }}</div>

            </div>
{{--            <a href="{{session()->has('shop_name') ?route("shops.products.index", ['shop' => session('shop_slug')])  : route("products.index") }}" class="text-button pb-1 border-b-2 border-black">View All Deals </a>--}}
            @if($link)
                <a href="{{ $link }}" class="text-button pb-1 border-b-2 border-black">View All Deals </a>
            @endif
        </div>
        <div
            class="list grid xl:grid-cols-5 lg:grid-cols-4 md:grid-cols-3 grid-cols-2 sm:gap-[30px] gap-[20px] md:mt-10 mt-6">
            @foreach ($items as $product)
                @include('partials.product-card', [
                                 '$product' => $product,
                             ])
            @endforeach


        </div>
    </div>
</div>
