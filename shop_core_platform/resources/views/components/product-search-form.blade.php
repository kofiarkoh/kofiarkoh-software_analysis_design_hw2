@php
    $actionUrl = session()->has('shop_id')
        ? route('shops.products.index', ['shop' => session('shop_slug')])
        : route('products.index');
@endphp

<form
    {{ $attributes->merge(['class' => 'product-search w-full flex items-center gap-0 bg-white overflow-visible']) }}
    method="GET" action="{{ $actionUrl }}"
>
    <div class="js-wrap relative flex-1 min-w-0 isolate ">
        <input
            type="text"
            class="js-input search-input h-[45px] px-4 w-full border border-line rounded-l"
            placeholder="What are you looking for today?"
            name="filter[name]"
            autocomplete="off"
            autocapitalize="off"
            autocorrect="off"
            spellcheck="false"
            value="{{ request('filter[name]') }}"
        />

        <ul class="js-list absolute left-0 right-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-md hidden
                   z-[9] max-h-64 overflow-auto touch-manipulation"></ul>
    </div>

    <button type="submit" class="search-button bg-[#fdd013] text-[#000080] h-[45px] px-7 rounded-none">
        Search
    </button>
</form>
