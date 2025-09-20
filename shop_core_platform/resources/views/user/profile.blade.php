<x-layout>
    <x-slot:title>
        My Account
    </x-slot>
    <div class="breadcrumb-block style-shared">
        <div class="breadcrumb-main bg-linear overflow-hidden">
            <div class="container lg:pt-[134px] pt-24 pb-10 relative">
                <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                    <div class="text-content">
                        <div class="heading2 text-center">My Account</div>
                        <div class="link flex items-center justify-center gap-1 caption1 mt-3">
                            <a href="/">Homepage</a>
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
                <x-user-profile-sidebar-menu active="myaccount"/>

                <div class="right list-filter md:w-2/3 w-full pl-2.5">
                    <div class="filter-item text-content w-full active" data-item="dashboard">
                        <div class="overview grid sm:grid-cols-3 gap-5">
                            <div
                                class="overview-item flex items-center justify-between p-5 border border-line rounded-lg box-shadow-xs">
                                <div class="counter">
                                    <span class="text-secondary">Awaiting Pickup</span>
                                    <h5 class="heading5 mt-1">4</h5>
                                </div>
                                <span class="ph ph-hourglass-medium text-4xl"></span>
                            </div>
                            <div
                                class="overview-item flex items-center justify-between p-5 border border-line rounded-lg box-shadow-xs">
                                <div class="counter">
                                    <span class="text-secondary">Cancelled Orders</span>
                                    <h5 class="heading5 mt-1">12</h5>
                                </div>
                                <span class="ph ph-receipt-x text-4xl"></span>
                            </div>
                            <div
                                class="overview-item flex items-center justify-between p-5 border border-line rounded-lg box-shadow-xs">
                                <div class="counter">
                                    <span class="text-secondary">Total Number of Orders</span>
                                    <h5 class="heading5 mt-1">200</h5>
                                </div>
                                <span class="ph ph-package text-4xl"></span>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="heading5 pb-4 mt-6">Information</div>

                            <div class="grid sm:grid-cols-2 gap-4 gap-y-5 mt-5">


                                <x-input name="first_name" label="First Name" :value="auth()->user()->first_name"
                                         required/>
                                <x-input name="last_name" label="Last Name" :value="auth()->user()->last_name"
                                         required/>
                                <x-input name="phone" label="Phone Number" :value="auth()->user()->phone" required/>
                                <x-input type="email" name="email" label="Email Address" :value="auth()->user()->email"
                                         required/>

                            </div>

                            <div class="block-button lg:mt-10 mt-6">
                                <button class="button-main">Save Changes</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
