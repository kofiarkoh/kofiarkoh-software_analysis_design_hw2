<x-layout>
    <x-slot:title>
        Shipping Address
    </x-slot>
    <div class="breadcrumb-block style-shared">
        <div class="breadcrumb-main bg-linear overflow-hidden">
            <div class="container lg:pt-[134px] pt-24 pb-10 relative">
                <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                    <div class="text-content">
                        <div class="heading2 text-center">Checkout</div>
                        <div class="link flex items-center justify-center gap-1 caption1 mt-3">
                            <a href="{{route('cart.index')}}">Cart</a>
                            <i class="ph ph-caret-right text-sm text-secondary2"></i>
                            <div class="text-secondary2 capitalize">Delivery Address</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container py-10">
        <h2 class="text-2xl font-bold mb-6">Select a New Delivery Region & City</h2>

        <form method="POST" action="{{ route('checkout.confirm-address') }}">
            @csrf

            @if ($errors->any())
                <div class="mb-4 text-red text-sm bg-red-50 border border-red-200 rounded px-4 py-3">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Region & City Dropdown --}}
            <div class="mt-8">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Region --}}
                    <div>
                        <label for="region" class="block mb-1 font-medium">Region</label>
                        <select id="region" class="w-full border rounded px-3 py-2" onchange="updateCities(this.value)">
                            <option value="">Select Region</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- City --}}
                    <div>
                        <label for="city_id" class="block mb-1 font-medium">City</label>
                        <select id="city_id" name="city_id" class="w-full border rounded px-3 py-2" onchange="updateFee(this.value)">
                            <option value="">Select City</option>
                        </select>
                    </div>
                </div>

                {{-- Delivery Fee --}}
                <div id="delivery-fee-box" class="mt-4 hidden">
                    <p class="text-sm text-gray-700">
                        <strong>Delivery Fee:</strong> GH₵ <span id="delivery-fee-amount">0.00</span>
                    </p>
                </div>
            </div>

            {{-- Delivery Instructions --}}
            <div class="mt-6">
                <label for="delivery_instructions" class="block mb-1 font-medium">Delivery Instructions (optional)</label>
                <textarea id="delivery_instructions" name="delivery_instructions" rows="3"
                          class="w-full border-line border-2 rounded px-3 py-2"
                          placeholder="E.g. Leave package at the front door">{{ old('delivery_instructions') }}</textarea>
            </div>

            {{-- Nearby City --}}
            <div class="mt-4">
                <label for="nearby_city" class="block mb-1 font-medium">Nearby Town or Landmark (optional)</label>
                <input type="text" name="nearby_city" id="nearby_city"
                       class="w-full border-line rounded px-3 py-2"
                       placeholder="E.g. Near Adum Market"
                       value="{{ old('nearby_city') }}">
            </div>

            <div class="mt-6">
                <button class="button-main w-full">Continue to Payment</button>
            </div>
        </form>

        {{--    @if ($addresses->isEmpty())--}}
{{--            <div class="bg-gray-100 p-6 rounded-lg text-center">--}}
{{--                <p class="text-gray-600">You have no saved addresses yet.</p>--}}
{{--                <a href="{{ route('addresses.create') }}" class="button-main mt-4 inline-block">Add New Address</a>--}}
{{--            </div>--}}
{{--        @else--}}
{{--            <form method="POST" action="{{route('checkout.confirm-address')}}">--}}
{{--                @csrf--}}
{{--                <div class="space-y-4">--}}
{{--                    @foreach ($addresses as $address)--}}
{{--                        <label class="block border border-line rounded-lg p-4 hover:border-black transition mt-4">--}}
{{--                            <input type="radio" name="address_id" value="{{ $address->id }}"--}}
{{--                                   class="mr-2" {{ $address->is_default ? 'checked' : '' }}>--}}

{{--                            <div class="font-semibold">--}}
{{--                                {{ $address->first_name }} {{ $address->last_name }}--}}
{{--                                @if ($address->is_default)--}}
{{--                                    <span class="text-green-600 text-xs font-semibold ml-2">(Default)</span>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                            <div class="text-sm text-gray-600 mt-1">--}}
{{--                                {{ $address->address }} <br>--}}
{{--                                {{ $address->city }}, {{ $address->region }}--}}
{{--                            </div>--}}
{{--                            <div class="text-sm text-gray-600 mt-1">--}}
{{--                                Phone: {{ $address->phone_number }}--}}
{{--                            </div>--}}
{{--                        </label>--}}
{{--                    @endforeach--}}
{{--                </div>--}}


{{--                <div class="mt-6">--}}
{{--                    <button class="button-main w-full">Continue to Payment</button>--}}
{{--                </div>--}}
{{--            </form>--}}
{{--        @endif--}}




    </div>


    @section('scripts')
        <script>
            const regionData = @json($regions); // Regions with nested cities and delivery_fee

            function updateCities(regionId) {
                const citySelect = document.getElementById('city_id');
                citySelect.innerHTML = '<option value="">Select City</option>';
                document.getElementById('delivery-fee-box').classList.add('hidden');

                const selectedRegion = regionData.find(r => r.id == regionId);
                if (!selectedRegion) return;

                selectedRegion.cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.id;
                    option.dataset.fee = city.delivery_fee;
                    option.textContent = `${city.name} (GH₵ ${parseFloat(city.delivery_fee).toFixed(2)})`;
                    citySelect.appendChild(option);
                });
            }

            function updateFee(cityId) {
                const citySelect = document.getElementById('city_id');
                const selected = citySelect.querySelector(`option[value="${cityId}"]`);

                if (selected && selected.dataset.fee) {
                    document.getElementById('delivery-fee-amount').textContent = parseFloat(selected.dataset.fee).toFixed(2);
                    document.getElementById('delivery-fee-box').classList.remove('hidden');
                } else {
                    document.getElementById('delivery-fee-box').classList.add('hidden');
                }
            }
        </script>
    @endsection


</x-layout>
