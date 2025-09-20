
<x-layout>
    <x-slot:title>
        Orders
    </x-slot>
    <div class="breadcrumb-block style-shared">
        <div class="breadcrumb-main bg-linear overflow-hidden">
            <div class="container lg:pt-[134px] pt-24 pb-10 relative">
                <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                    <div class="text-content">
                        <div class="heading2 text-center">My Orders</div>
                        <div class="link flex items-center justify-center gap-1 caption1 mt-3">
                            <a href="/">Homepage</a>
                            <i class="ph ph-caret-right text-sm text-secondary2"></i>
                            <div class="text-secondary2 capitalize">My Orders</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="my-account-block md:py-20 py-10">
        <div class="container">
            <div class="content-main lg:px-[60px] md:px-4 flex gap-y-8 max-md:flex-col w-full">
                <x-user-profile-sidebar-menu active="orders" />
                <div class="right list-filter md:w-2/3 w-full pl-2.5">
                    <div class="filter-item tab_order text-content overflow-hidden w-full p-7 border border-line rounded-xl active" data-item="orders">
                        <h6 class="heading6">Your Orders</h6>

                        @foreach ($orders as $order)
                            <div class="border p-4 rounded mb-4 shadow-sm bg-white mt-4">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div><strong>Order #{{ $order->id }}</strong></div>
                                        <div class="text-sm text-gray-600">Placed on {{ $order->created_at->format('M d, Y') }}</div>
                                        <div class="text-sm text-gray-600">Status: {{ ucfirst($order->status) }}</div>
                                        <div class="text-sm font-bold mt-1">GH₵ {{ number_format($order->total_price, 2) }}</div>
                                    </div>
                                    <a href="{{ route('user.orders.show', $order) }}" class="button-main px-4 py-2 text-sm">View Details</a>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>

