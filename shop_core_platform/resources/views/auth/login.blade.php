<x-layout>
    <x-slot:title>
        Login
    </x-slot>
    <div class="breadcrumb-block style-shared">
        <div class="breadcrumb-main bg-[#0000801]  overflow-hidden">
            <div class="container lg:pt-[134px] pt-24 pb-10 relative">
                <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                    <div class="text-content">
                        <div class="heading2 text-center">Login</div>
                        <div class="link flex items-center justify-center gap-1 caption1 mt-3">
                            <a href="/">Homepage</a>
                            <i class="ph ph-caret-right text-sm text-secondary2"></i>
                            <div class="text-secondary2 capitalize">Login</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="login-block md:py-20 py-10">
        <div class="container">
            <div class="content-main flex gap-y-8 max-md:flex-col">
                <div class="left md:w-1/2 w-full lg:pr-[60px] md:pr-[40px] md:border-r border-line">
                    <div class="heading4">Login</div>
                    <form class="md:mt-7 mt-4" method="post" action="{{ route('login') }}">
                        @csrf
                        <div class="email">
                            <x-input
                                type="email"
                                name="email"
                                placeholder="Email *"
                                required
                            />
                        </div>
                        <div class="pass mt-5">
                            <x-input
                                type="password"
                                name="password"
                                placeholder="Password *"
                                required
                            />

                        </div>
                        <div class="flex items-center justify-between mt-5">
                            <div class="flex items-center">
                                <div class="block-input">
                                    <input type="checkbox" name="remember" id="remember" />
                                    <i class="ph-fill ph-check-square icon-checkbox text-2xl"></i>
                                </div>
                                <label for="remember" class="pl-2 cursor-pointer">Remember me</label>
                            </div>
                            <a href="{{route("auth.password-reset.request")}}" class="font-semibold hover:underline">Forgot Your Password? </a>
                        </div>
                        <div class="block-button md:mt-7 mt-4">
                            <button class="button-main">Login</button>
                        </div>
                    </form>
                </div>
                <div class="right md:w-1/2 w-full lg:pl-[60px] md:pl-[40px] flex items-center">
                    <div class="text-content">
                        <div class="heading4">New Customer</div>
                        <div class="mt-2 text-secondary">Be part of our growing family of new customers! Join us today and unlock a world of exclusive benefits, offers, and personalized experiences.</div>
                        <div class="block-button md:mt-7 mt-4">
                            <a href="{{ route("auth.register-page") }}" class="button-main">Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
