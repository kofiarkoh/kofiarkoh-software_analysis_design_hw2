<a href="{{ route('products.detail', ['product' => $product->id]) }}">
    <div class="product-item style-marketplace p-4 border border-line rounded-2xl">
        <div class="bg-img relative w-full flex justify-center items-center">
            <img class="product-image" src="{{ Storage::disk('public')->url($product->photos[0]) }}"
                 alt=""/>
        </div>
        <div class="product-infor mt-4">
            <span class="text-title ellipsis">{{$product->name}}</span>
            <div class="flex gap-0.5 mt-1">
                <i class="ph-fill ph-star text-sm text-yellow"></i>
                <i class="ph-fill ph-star text-sm text-yellow"></i>
                <i class="ph-fill ph-star text-sm text-yellow"></i>
                <i class="ph-fill ph-star text-sm text-yellow"></i>
                <i class="ph-fill ph-star text-sm text-yellow"></i>
            </div>
            <span class="text-title inline-block mt-1">GH₵ {{ $product->price }}</span>
        </div>
    </div>
</a>
