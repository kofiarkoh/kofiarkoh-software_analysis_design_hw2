<x-layout>
    <x-slot:title>
        Orders
    </x-slot>
    <div class="container mx-auto px-4 md:px-8 py-6">
        <div class="bg-white p-6 rounded shadow-sm mb-6">
            <h2 class="text-lg font-semibold mb-2">Order #{{ $order->id }}</h2>
            <p class="text-sm text-gray-600">Status: {{ ucfirst($order->status) }}</p>
            <p class="text-sm text-gray-600">Placed on: {{ $order->created_at->format('F j, Y') }}</p>
            <p class="text-sm text-gray-600">Payment Method: {{ ucfirst($order->payment_method) }}</p>
        </div>

        @foreach ($order->items as $item)
            <div class="flex items-center gap-4 border-b py-4">
                @if ($item->product->image)
                    <img src="{{ asset('storage/' . $item->product->image) }}" class="w-16 h-16 object-cover rounded" />
                @endif
                <div>
                    <div class="font-semibold">{{ $item->product->name }}</div>
                    @if ($item->variant)
                        <div class="text-sm text-gray-500">
                            @foreach ($item->variant->attributeValues as $val)
                                {{ $val->attribute->name }}: {{ $val->value }}{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </div>
                    @endif
                    <div class="text-sm text-gray-600">Qty: {{ $item->quantity }}</div>
                    <div class="text-sm font-semibold">Price: GH₵ {{ number_format($item->price, 2) }}</div>
                </div>
            </div>
        @endforeach

        <div class="text-right mt-6 text-lg font-bold">
            Total: GH₵ {{ number_format($order->total_price, 2) }}
        </div>

        <div class="bg-white p-6 rounded shadow-sm mb-6">
            <h3 class="text-md font-semibold mb-2">Shipping Address</h3>

            @if ($order->address)
                <div class="text-sm text-gray-700 leading-relaxed">
                    <p>{{ $order->address->first_name }} {{ $order->address->last_name }}</p>
                    <p>{{ $order->address->phone_number }}</p>
                    @if ($order->address->additional_phone_number)
                        <p>{{ $order->address->additional_phone_number }}</p>
                    @endif
                    <p>{{ $order->address->address }}</p>
                    @if ($order->address->additional_info)
                        <p class="text-gray-500">{{ $order->address->additional_info }}</p>
                    @endif
                    <p>{{ $order->address->city }}, {{ $order->address->region }}</p>
                </div>
            @else
                <p class="text-sm text-gray-500">No shipping address associated with this order.</p>
            @endif
        </div>


    </div>
</x-layout>
