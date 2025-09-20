@props([
    'name',
    'max' => 5,
    'min' => 0,
    'value' => 0,
])

@if ($max == 0)
    <span class="text-sm text-red-600 font-semibold">Out of Stock</span>
@else
    <div
        x-data="{ qty: {{ $value }} }"
        class="flex items-center gap-2 border border-gray-300 rounded px-2 py-1 w-fit"
    >
        <button
            type="button"
            class="px-2 py-1 text-lg font-bold"
            @click="qty = Math.max(qty - 1, {{ $min }})"
        >−</button>

        <div class="w-10 text-center select-none" x-text="qty"></div>

        <button
            type="button"
            class="px-2 py-1 text-lg font-bold"
            @click="qty = Math.min(qty + 1, {{ $max }})"
        >+</button>

        <input type="hidden" :value="qty" name="{{ $name }}" />
    </div>
@endif
