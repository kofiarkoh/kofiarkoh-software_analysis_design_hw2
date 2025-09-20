<x-layout>
    <x-slot:title>
        Payment
    </x-slot>

    <div class="cart-block md:py-20 py-10">
        <div class="container py-10 max-w-2xl mx-auto">
            <div class="content-main flex justify-between max-xl:flex-col gap-y-8 ">
                <div class="xl:w-2/3 xl:pr-3">


                    <h2 class="text-2xl font-bold mb-6">Payment</h2>

                    <form method="POST" action="{{ route('checkout.payment.process') }}">
                        @csrf

                        <div class="mb-6 mt-6">
                            <h3 class="text-lg font-semibold mb-2">Shipping to:</h3>
                            {{--                <div class="text-sm text-gray-700">--}}
                            {{--                    {{ $address->first_name }} {{ $address->last_name }}<br>--}}
                            {{--                    {{ $address->address }}, {{ $address->city }}, {{ $address->region }}<br>--}}
                            {{--                    Phone: {{ $address->phone_number }}--}}
                            {{--                </div>--}}
                            <span class="text-gray-500">
                    Delivery City: {{ $city->name }}<br>
                    Delivery Fee: GH₵ {{ number_format($city->delivery_fee, 2) }}
                </span>
                        </div>

                        <div class="mb-6 mt-6">
                            <h3 class="text-lg font-semibold mb-2">Select Payment Method</h3>

                            <label
                                class="flex items-center border border-line rounded-lg px-4 py-3 cursor-pointer gap-4 mt-4">
                                <input type="radio" name="payment_method" value="mobile_money" class="mr-3" checked>

                                <div class="flex flex-col">
                                    <div class="font-medium text-gray-800 mb-2">Mobile Money</div>
                                    <div class="flex items-center gap-6 mt-3">
                                        {{-- MTN --}}
                                        <div class="flex items-center gap-2">
                                            <img src="{{ asset('images/momo/mtn.jpg') }}" alt="MTN MoMo"
                                                 class="h-20 w-auto">
                                            {{--                                <span class="text-sm text-gray-700">MTN MoMo</span>--}}
                                        </div>

                                        {{-- Telecel --}}
                                        <div class="flex items-center gap-2">
                                            <img src="{{ asset('images/momo/telecel.jpeg') }}" alt="Telecel Cash"
                                                 class="h-12 w-auto">
                                            {{--                                <span class="text-sm text-gray-700">Telecel Cash</span>--}}
                                        </div>

                                        {{-- AirtelTigo --}}
                                        <div class="flex items-center gap-2">
                                            <img src="{{ asset('images/momo/airteltigo.jpg') }}" alt="AirtelTigo Money"
                                                 class="h-12 w-auto">
                                            {{--                                <span class="text-sm text-gray-700">AirtelTigo Money</span>--}}
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <div class="flex justify-between items-center mt-6">
                            <span class="text-lg font-semibold"></span>
                            <button class="button-main">Pay Now</button>
                        </div>
                    </form>
                </div>


        @if ($cartItems->isNotEmpty())
            <div class="xl:w-1/3 xl:pl-12 w-full">

                <div class="checkout-block bg-surface p-6 rounded-2xl">
                    <div class="heading5">Order Summary</div>
                    <div class="total-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Subtotal</div>
                        <div class="text-title">GH₵ <span
                                class="total-product">{{ auth()->user()?->cartTotalPrice() ?? '0.00' }}</span></div>
                    </div>
                    <div class="discount-block py-5 flex justify-between border-b border-line">
                        <div class="text-title">Delivery Fee</div>
                        <div class="text-title"><span>GH₵ </span><span class="discount">{{$deliveryFee}}</span><span></span></div>
                    </div>

                    <div class="total-cart-block pt-4 pb-4 flex justify-between">
                        <div class="heading5">Total</div>
                        <div class=""><span class="heading5">GH₵ </span><span
                                class="total-cart heading5">{{ $totalAmount }}</span>
                        </div>
                    </div>

                </div>

            </div>
        @endif
            </div>
        </div>
    </div>
</x-layout>
