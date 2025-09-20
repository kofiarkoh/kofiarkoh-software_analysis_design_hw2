<x-layout>
    <x-slot:title>
        Addresses
    </x-slot>
    <div class="breadcrumb-block style-shared">
        <div class="breadcrumb-main bg-linear overflow-hidden">
            <div class="container lg:pt-[134px] pt-24 pb-10 relative">
                <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                    <div class="text-content">
                        <div class="heading2 text-center">My Account</div>
                        <div class="link flex items-center justify-center gap-1 caption1 mt-3">
                            <a href="index.html">Homepage</a>
                            <i class="ph ph-caret-right text-sm text-secondary2"></i>
                            <div class="text-secondary2 capitalize">My Account</div>
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


                        <form method="POST" action="{{ isset($address) ? route('addresses.update', ['address' => $address]) : route('addresses.store') }}">
                            @csrf
                            @if(isset($address))
                                @method('PUT')
                            @endif

                            <div class="heading5 pb-4 mt-6">{{ isset($address) ? 'Update' : 'Add New' }} Address</div>

                            <div class="grid sm:grid-cols-2 gap-4 gap-y-5 mt-5">
                                <x-input name="first_name" label="First Name" :value="old('first_name', $address->first_name ?? '')" required />
                                <x-input name="last_name" label="Last Name" :value="old('last_name', $address->last_name ?? '')" required />

                                <x-input name="phone_number" label="Phone Number" :value="old('phone_number', $address->phone_number ?? '')" required />
                                <x-input name="additional_phone_number" label="Additional Phone Number" :value="old('additional_phone_number', $address->additional_phone_number ?? '')" />

                                <x-input name="address" label="Address" :value="old('address', $address->address ?? '')" required />
                                <x-input name="additional_info" label="Additional Information" :value="old('additional_info', $address->additional_info ?? '')" />

                                @php
                                    $ghanaRegions = [
                                        'Ahafo', 'Ashanti', 'Bono', 'Bono East', 'Central', 'Eastern', 'Greater Accra',
                                        'North East', 'Northern', 'Oti', 'Savannah', 'Upper East', 'Upper West',
                                        'Volta', 'Western', 'Western North'
                                    ];
                                @endphp

                                <div class="sm:col-span-2">
                                    <label for="region" class="block text-sm font-medium text-gray-700">Region <span class="text-red-500">*</span></label>
                                    <select id="region" name="region" required
                                            class="mt-2 block w-full rounded-lg border border-line px-4 py-2 text-sm">
                                        <option value="">Select a region</option>
                                        @foreach ($ghanaRegions as $region)
                                            <option value="{{ $region }}"
                                                {{ old('region', $address->region ?? '') === $region ? 'selected' : '' }}>
                                                {{ $region }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>


                                <x-input name="city" label="City" :value="old('city', $address->city ?? '')" required />
                            </div>

                            <div class="flex items-center mt-4">
                                <input type="checkbox" id="is_default" name="is_default" value="1" class="mr-2"
                                    {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
                                <label for="is_default" class="text-sm text-gray-700">Set as Default Address</label>
                            </div>

                            <div class="block-button lg:mt-10 mt-6">
                                <button class="button-main">
                                    {{ isset($address) ? 'Update Address' : 'Save Address' }}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
