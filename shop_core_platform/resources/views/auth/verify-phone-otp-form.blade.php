<x-layout>
    <x-slot:title>
        Verify Phone Number
    </x-slot>
    <div class="breadcrumb-block style-shared">
        <div class="breadcrumb-main bg-linear overflow-hidden">
            <div class="container lg:pt-[134px] pt-24 pb-10 relative">
                <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                    <div class="text-content">
                        <div class="heading2 text-center">Verify OTP</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="login-block md:py-20 py-10">
        <div class="container">
            <div class="content-main flex gap-y-8 max-md:flex-col">
                <div class="w-full lg:pl-[60px] md:pl-[40px] flex justify-center items-center">
                    <div class="text-content text-center">
                        <div class="block-button md:mt-7 mt-4">
                            <form method="POST" action="{{ route('verify.phone.submit') }}">
                                @csrf
                                <div class=" my-4">
                                    <p class="mb-2">Enter the OTP token sent to your phone number </p>
                                 <div class="mt-2">
                                     <x-input
                                         type="text"
                                         name="token"
                                         placeholder="Enter OTP *"
                                         required
                                     />
                                 </div>
                                </div>
                                <button class="button-main mt-4">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
