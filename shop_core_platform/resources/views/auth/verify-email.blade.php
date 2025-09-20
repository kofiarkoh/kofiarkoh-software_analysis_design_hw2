<x-layout>
    <x-slot:title>
        Verify Email
    </x-slot>
    <div class="breadcrumb-block style-shared">
        <div class="breadcrumb-main bg-linear overflow-hidden">
            <div class="container lg:pt-[134px] pt-24 pb-10 relative">
                <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                    <div class="text-content">
                        <div class="heading2 text-center">We Need To Verify Your Email</div>
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
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf

                                <button class="button-main">Send Verification Link</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
