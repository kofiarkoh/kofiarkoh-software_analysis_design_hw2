<div>
    @foreach ($categories as $category)

        <a href="products?filter[categories.slug]={{$category->slug}}" class="item py-3 whitespace-nowrap border-b border-line w-full flex items-center justify-between">
                                        <span class="flex items-center gap-2">
                                            <i class="ph-bold {{$category->icon}} text-lg"></i>
                                            <span class="">{{ $category->name }}</span>
                                        </span>
            <i class="ph-bold ph-caret-right"></i>
        </a>

    @endforeach
</div>
