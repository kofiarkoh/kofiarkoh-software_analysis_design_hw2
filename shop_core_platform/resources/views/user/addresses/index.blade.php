<x-layout>
    <x-slot:title>
        Addresses
    </x-slot>
    <div class="breadcrumb-block style-shared">
        <div class="breadcrumb-main bg-linear overflow-hidden">
            <div class="container lg:pt-[134px] pt-24 pb-10 relative">
                <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                    <div class="text-content">
                        <div class="heading2 text-center">My Addresses</div>
                        <div class="link flex items-center justify-center gap-1 caption1 mt-3">
                            <a href="/">Homepage</a>
                            <i class="ph ph-caret-right text-sm text-secondary2"></i>
                            <div class="text-secondary2 capitalize">My Addresses</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="my-account-block md:py-20 py-10">
        <div class="container">
            <div class="content-main lg:px-[60px] md:px-4 flex gap-y-8 max-md:flex-col w-full">
                <x-user-profile-sidebar-menu active="address"/>

                <div class="right list-filter md:w-2/3 w-full pl-2.5">
                    <div class="filter-item text-content w-full active" data-item="address">


                        <div class="filter-item text-content w-full active" data-item="address">

                            {{-- New Address Link --}}
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold">Your Addresses</h2>
                                <a href="{{ route('addresses.create') }}"
                                   class="text-sm px-4 py-2 rounded-md bg-black text-white hover:bg-gray-800 transition">
                                    + Add New Address
                                </a>
                            </div>

                            <div class="space-y-4">
                                @forelse ($addresses as $address)
                                    @php
                                        $isDefault = $address->is_default;
                                    @endphp

                                    <div class="w-full {{ $isDefault ? ' bg-green-50' : 'border-line bg-white' }} border rounded-lg p-4 shadow-sm relative mt-3">
                                        {{-- Default Badge --}}
                                        @if ($isDefault)
                                            <span class="absolute top-0 right-0  text-white text-xs font-bold px-2 py-1 rounded-bl-md">
            Default
        </span>
                                        @endif

                                        {{-- Name --}}
                                        <div class="text-base font-semibold mb-1 flex items-center gap-2">
                                            {{ $address->first_name }} {{ $address->last_name }}
                                            @if ($isDefault)
                                                <span class="text-xs  font-semibold">(Default)</span>
                                            @endif
                                        </div>

                                        {{-- Phone Numbers --}}
                                        <div class="text-sm text-gray-600">
                                            {{ $address->phone_number }}
                                            @if ($address->additional_phone_number)
                                                <br>{{ $address->additional_phone_number }}
                                            @endif
                                        </div>

                                        {{-- Address Info --}}
                                        <div class="text-sm text-gray-700 mt-2">
                                            {{ $address->address }}<br>
                                            @if($address->additional_info)
                                                <span class="text-gray-500">{{ $address->additional_info }}</span><br>
                                            @endif
                                            {{ $address->city }}, {{ $address->region }}
                                        </div>

                                        {{-- Actions --}}
                                        <div class="flex justify-between items-center mt-4">
                                            <div >

                                            </div>

                                            <a href="{{ route('addresses.edit', ['address' => $address]) }}"
                                               class="text-sm text-blue-600 underline">
                                                Edit
                                            </a>
{{--                                            <form method="POST" action="{{ route('addresses.destroy', $address) }}"--}}
{{--                                                  onsubmit="return confirm('Are you sure you want to delete this address?')">--}}
{{--                                                @csrf--}}
{{--                                                @method('DELETE')--}}
{{--                                                <button type="submit" class="text-sm text-red hover:underline">--}}
{{--                                                    Delete--}}
{{--                                                </button>--}}
{{--                                            </form>--}}
                                        </div>
                                    </div>

                                @empty
                                    {{-- No Addresses Message --}}
                                    <div class="bg-gray-50 border border-dashed border-gray-300 p-6 rounded-lg text-center">
                                        <p class="text-gray-600">You haven’t added any addresses yet.</p>
                                        <a href="{{ route('addresses.create') }}"
                                           class="inline-block mt-4 px-4 py-2 bg-black text-white text-sm rounded-md hover:bg-gray-800 transition">
                                            + Add Your First Address
                                        </a>
                                    </div>
                                @endforelse
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
