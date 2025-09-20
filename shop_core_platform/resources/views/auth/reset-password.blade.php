<x-layout>
    <x-slot:title>
        Reset Password
    </x-slot>

    <div class="breadcrumb-block style-shared">
        <div class="breadcrumb-main bg-linear overflow-hidden">
            <div class="container lg:pt-[134px] pt-24 pb-10 relative">
                <div class="main-content w-full h-full flex flex-col items-center justify-center relative z-[1]">
                    <div class="text-content">
                        <div class="heading2 text-center">Reset Password</div>
                        <div class="link flex items-center justify-center gap-1 caption1 mt-3">
                            <a href="{{ route('homepage') }}">Homepage</a>
                            <i class="ph ph-caret-right text-sm text-secondary2"></i>
                            <div class="text-secondary2 capitalize">Reset Password</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="forgot-pass md:py-20 py-10">
        <div class="container">
            <div class="content-main flex gap-y-8 max-md:flex-col">
                <div class="left md:w-1/2 w-full lg:pr-[60px] md:pr-[40px] md:border-r border-line">
                    <div class="heading4">Enter your new password</div>

                    @if ($errors->any())
                        <div class="text-red text-sm mt-4">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="md:mt-7 mt-4" method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ old('email', $email) }}">

                        <div class="mb-4">
                            <x-input
                                type="password"
                                name="password"
                                placeholder="New Password *"
                                required
                            />
                        </div>

                        <div class="mb-4 mt-4">
                            <x-input
                                type="password"
                                name="password_confirmation"
                                placeholder="Confirm Password *"
                                required
                            />
                        </div>

                        <div class="block-button md:mt-7 mt-4">
                            <button class="button-main">Reset Password</button>
                        </div>
                    </form>
                </div>

                <div class="right md:w-1/2 w-full lg:pl-[60px] md:pl-[40px] flex items-center">
                    <div class="text-content">
                        <div class="heading4">Remembered it?</div>
                        <div class="mt-2 text-secondary">You can go back to login if you remembered your password.</div>
                        <div class="block-button md:mt-7 mt-4">
                            <a href="{{ route('auth.login-page') }}" class="button-main">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
