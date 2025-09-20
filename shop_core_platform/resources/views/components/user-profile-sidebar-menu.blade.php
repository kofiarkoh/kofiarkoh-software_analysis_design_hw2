<div class="left md:w-1/3 w-full xl:pr-[3.125rem] lg:pr-[28px] md:pr-[16px]">
    <div class="user-infor bg-surface md:px-8 px-5 md:py-10 py-6 md:rounded-[20px] rounded-xl">
        <div class="heading flex flex-col items-center justify-center">
            <div class="avatar">
                <img src="{{ asset('images/user_avatar.png') }}" alt="avatar"
                     class="md:w-[140px] w-[120px] md:h-[140px] h-[120px] object-contain" />
            </div>
            <div class="name heading6 mt-4 text-center">{{auth()->user()->first_name}} {{auth()->user()->last_name}}</div>
            <div class="mail heading6 font-normal normal-case text-secondary text-center mt-1">{{auth()->user()->email}}</div>
        </div>
        <div class="menu-tab list-category w-full max-w-none lg:mt-10 mt-6">
            @php
                $items = [
                    ['label' => 'My Account', 'icon' => 'ph-house-line', 'item' => 'myaccount', 'href' => route('user.profile')],
                    ['label' => 'Order History', 'icon' => 'ph-package', 'item' => 'orders', 'href' => route('user.orders')],
                    ['label' => 'My Address', 'icon' => 'ph-navigation-arrow', 'item' => 'address', 'href' => route('addresses.index')],
                ];
            @endphp

            @foreach ($items as $item)
                <a href="{{ $item['href'] }}"
                   class="category-item flex items-center gap-3 w-full px-5 py-4 rounded-lg cursor-pointer duration-300 hover:bg-white mt-1.5 {{ $active === $item['item'] ? 'active bg-white' : '' }}"
                   data-item="{{ $item['item'] }}">
                    <span class="ph {{ $item['icon'] }} text-xl"></span>
                    <strong class="heading6">{{ $item['label'] }}</strong>
                </a>
            @endforeach
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="category-item flex items-center gap-3 w-full px-5 py-4 rounded-lg cursor-pointer duration-300 hover:bg-white mt-1.5 bg-transparent text-left {{ request()->routeIs('logout') ? 'active bg-white' : '' }}">
                    <span class="ph ph-sign-out text-xl"></span>
                    <strong class="heading6">Logout</strong>
                </button>
            </form>

        </div>
    </div>
</div>
