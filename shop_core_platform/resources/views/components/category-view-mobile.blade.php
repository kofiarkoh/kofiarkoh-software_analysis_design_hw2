<div>
    @foreach ($categories as $category)

        <li>
            <a href="products?filter[categories.slug]={{$category->slug}}" class=" flex items-center justify-between mt-5"
            >
                <span class="text-left">
                    <i class="ph {{$category->icon}}  "></i>
                    {{ $category->name }}
                </span>

                <span class="text-right">
                    <i class="ph ph-caret-right text-xl"></i>
                </span>
            </a>

        </li>

    @endforeach
</div>
