<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> {{$title}} | {{env('APP_NAME')}}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/fav.png') }}" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/output-scss.css') }}" />
    <link rel="stylesheet" href="{{ asset('dist/output-tailwind.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/custom-styles.css') }}" />
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    @yield('styles')
</head>

<body>



@if(env('APP_ENV') !== 'production')
    <div class="corner-ribbon">STAGING</div>
@endif

<div id="header" class="relative w-full style-marketplace">
    <div class="header-menu style-marketplace relative bg-[#000080]  w-full md:h-[74px] h-[56px]">
        <div class="container mx-auto h-full">
            <div class="header-main flex items-center justify-between h-full">
                <div class="menu-mobile-icon lg:hidden flex items-center">
                    <i class="icon-category text-white text-2xl"></i>
                </div>
                <a href="/" class="flex items-center text-white font-bold">
{{--                    <h4 class="heading4 text-white">{{env('APP_NAME')}}sd</h4>--}}
                    SHOP9
                </a>
                <div class="form-search w-2/3 pl-8 flex items-center h-[44px] max-lg:hidden">
                        <x-product-search-form/>
                </div>
                <div class="right flex gap-12 z-[1]">
                    <div class="list-action flex items-center gap-4">
                        <div class="user-icon flex items-center justify-center cursor-pointer">
                            <i class="ph-bold ph-user text-white text-2xl"></i>

                            @guest
                                <div class="login-popup  z-[99999999] absolute top-[74px] w-[320px] p-7 rounded-xl bg-white">
                                    <a href="{{ route('auth.login-page') }}" class="button-main w-full text-center">Login</a>
                                    <div class="text-secondary text-center mt-3 pb-4">
                                        Don’t have an account?
                                        <a href="{{ route('auth.register-page') }}" class="text-black pl-1 hover:underline">Register</a>
                                    </div>
                                </div>
                            @endguest

                            @auth
                                <div class="login-popup absolute top-[74px] w-[320px] p-7 rounded-xl bg-white">

                                    <div class="bottom mt-4 pt-4 border-t border-line"></div>
                                    <a href="{{ route('profile.update') }}" class="body1 hover:underline">My Account</a>
                                </div>
                            @endauth
                        </div>
                        <a href="{{ route('cart.index') }}" class="cart-icon flex items-center justify-center cursor-pointer">
                            <div class="cart-icon flex items-center relative cursor-pointer">
                                <i class="ph-bold ph-handbag text-white text-2xl"></i>
                                <span class="quantity cart-quantity absolute -right-1.5 -top-1.5 text-xs text-black bg-[#fdd013] w-4 h-4 flex items-center justify-center rounded-full">
                                {{ auth()->user()?->cartItemCount() ?? 0 }}
                            </span>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        @if(session()->has('shop_name'))
            <div id="top-nav" class="top-nav bg-[#263587] md:h-[44px] h-[30px] border-b border-surface1">
                <div class="container mx-auto h-full">
                    <div class="top-nav-main flex justify-between max-md:justify-center h-full">
                        <div class="left-content flex items-center gap-5 max-md:hidden">

                        </div>
                        <div class="text-center text-button-uppercase text-white flex items-center">You are browsing products from: {{session('shop_name')}}</div>
                        <div class="right-content flex items-center gap-5 max-md:hidden">

                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{--        MOBILE SEARCH BAR--}}
        <x-product-search-form class="block lg:hidden" />


    </div>

    <div class="top-nav-menu relative bg-white border-b border-line h-[44px] max-lg:hidden z-10">
        <div class="container h-full">
            <div class="top-nav-menu-main flex items-center justify-between h-full">
                <div class="left flex items-center h-full">
                    @yield('homeCategories')
                     <div class="menu-main style-eight h-full pl-12 max-lg:hidden">
                        <ul class="flex items-center gap-8 h-full">
                            <li class="h-full relative">
                                <a href="#!" class="text-button-uppercase duration-300 h-full flex items-center justify-center"> Vendors </a>
                                <div class="sub-menu py-3 px-5 -left-10 absolute bg-white rounded-b-xl">
                                    <ul class="w-full">
                                        <li>
                                            <a href="{{ route('filament.vendor.auth.login') }}" class="link text-secondary duration-300">Login As Vendor</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('filament.vendor.auth.register') }}" class="link text-secondary duration-300">Register as Vendor</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="right flex items-center gap-1">
                    <div class="caption1">Hotline:</div>
                    <div class="text-button-uppercase">+1-203-203-203</div>
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Mobile -->
    <div id="menu-mobile" class="">
        <div class="menu-container bg-white h-full">
            <div class="container h-full">
                <div class="menu-main h-full overflow-hidden">
                    <div class="heading py-2 relative flex items-center justify-center">
                        <div class="close-menu-mobile-btn absolute left-0 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-surface flex items-center justify-center">
                            <i class="ph ph-x text-sm"></i>
                        </div>
                        <a href="/" class="logo text-3xl font-semibold text-center">{{env('APP_NAME')}}</a>
                    </div>
                    <div class="list-nav mt-6">
                        <ul>
                            <div class="mt-3">
                                <hr />
                                <h2 class="my-2">Categories</h2>
                                <hr/>
                            </div>
                            <x-category-view viewType="mobile" />
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu bar -->
{{--    <div class="menu_bar fixed bg-white bottom-0 left-0 w-full h-[70px] sm:hidden z-[101]">--}}
{{--        <div class="menu_bar-inner grid grid-cols-4 items-center h-full">--}}
{{--            <a href="index.html" class="menu_bar-link flex flex-col items-center gap-1">--}}
{{--                <span class="ph-bold ph-house text-2xl block"></span>--}}
{{--                <span class="menu_bar-title caption2 font-semibold">Home</span>--}}
{{--            </a>--}}
{{--            <a href="shop-filter-canvas.html" class="menu_bar-link flex flex-col items-center gap-1">--}}
{{--                <span class="ph-bold ph-list text-2xl block"></span>--}}
{{--                <span class="menu_bar-title caption2 font-semibold">Category</span>--}}
{{--            </a>--}}
{{--            <a href="search-result.html" class="menu_bar-link flex flex-col items-center gap-1">--}}
{{--                <span class="ph-bold ph-magnifying-glass text-2xl block"></span>--}}
{{--                <span class="menu_bar-title caption2 font-semibold">Search</span>--}}
{{--            </a>--}}
{{--            <a href="cart.html" class="menu_bar-link flex flex-col items-center gap-1">--}}
{{--                <div class="cart-icon relative">--}}
{{--                    <span class="ph-bold ph-handbag text-2xl block"></span>--}}
{{--                    <span class="quantity cart-quantity absolute -right-1.5 -top-1.5 text-xs text-white bg-black w-4 h-4 flex items-center justify-center rounded-full">0</span>--}}
{{--                </div>--}}
{{--                <span class="menu_bar-title caption2 font-semibold">Cart</span>--}}
{{--            </a>--}}
{{--        </div>--}}
{{--    </div>--}}

    @yield('homeSlider')
</div>



{{ $slot }}



<a class="scroll-to-top-btn" href="#top-nav"><i class="ph-bold ph-caret-up"></i></a>


<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/magnific-popup.min.js') }}"></script>
<script src="{{ asset('assets/js/phosphor-icons.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script>
   $(document).ready(function () {



       document.addEventListener('DOMContentLoaded', function () {
           var notyf = new Notyf({
               position: {x:'center',y:'top'},
               ripple: false
           });

           @if (session('success'))
           notyf.success("{{ session('success') }}");
           @endif

           @if (session('error'))
           notyf.error("{{ session('error') }}");
           @endif

           @if (session('warning'))
           notyf.open({ type: 'warning', message: "{{ session('warning') }}" });
           @endif

           @if (session('info'))
           notyf.open({ type: 'info', message: "{{ session('info') }}" });
           @endif
       });
   })
</script>

<script>

    (function ($) {
        const debounce = (fn, ms) => { let t; return function(){ clearTimeout(t); t=setTimeout(() => fn.apply(this, arguments), ms); }; };
        const xhrByRoot = new WeakMap();

        function upsertHidden($form, name, value) {
            let $hidden = $form.find('input[type=hidden][name="'+name+'"]');
            if (!$hidden.length) $hidden = $('<input/>', { type: 'hidden', name }).appendTo($form);
            $hidden.val(value);
        }

        function render($list, items){
            $list.empty();
            if (!Array.isArray(items) || !items.length) return $list.addClass('hidden');

            items.forEach(item => {
                const label = item?.label ?? '';
                const $li = $('<li/>', {
                    class: 'px-4 py-2 cursor-pointer hover:bg-gray-100',
                    'data-label': label,
                    text: label   // safe text (no HTML)
                });
                $li.appendTo($list);
            });

            $list.removeClass('hidden');
        }

        $(document).on(
            'input keyup change paste compositionend search focus',
            '.product-search .js-input',
            debounce(function () {
                const $input = $(this);
                const $root  = $input.closest('.product-search');
                const $wrap  = $input.closest('.js-wrap');
                const $list  = $wrap.find('.js-list');
                const q      = $input.val().trim();

                if (q.length < 2) return $list.addClass('hidden').empty();

                const prev = xhrByRoot.get($root[0]);
                if (prev) prev.abort();

                const xhr = $.ajax({
                    url: '/search-suggestions',
                    data: { query: q },
                    dataType: 'json',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: (data)=> render($list, data),
                    error:   ()=> $list.addClass('hidden').empty()
                });
                xhrByRoot.set($root[0], xhr);
            }, 150)
        );

        // Always perform a name search on click
        $(document).on('mousedown touchstart', '.product-search .js-list li', function (e) {
            e.preventDefault();

            const $li   = $(this);
            const label = $li.data('label') || $li.text();

            const $wrap = $li.closest('.js-wrap');
            const $form = $wrap.closest('form');
            const $inp  = $wrap.find('.js-input');
            const $list = $wrap.find('.js-list');

            // Show clicked suggestion in the visible input
            $inp.val(label);

            // Ensure the request includes q=<label> (even if your input name isn't "q")
            upsertHidden($form, 'q', label);

            // Hide dropdown and submit
            $list.addClass('hidden').empty();
            $form.trigger('submit');
        });

        // Hide dropdown when clicking outside / ESC
        $(document).on('touchstart click', function (e) {
            if (!$(e.target).closest('.product-search .js-wrap').length) {
                $('.product-search .js-list').addClass('hidden');
            }
        });
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape') $('.product-search .js-list').addClass('hidden');
        });
    })(jQuery);


</script>
@yield('scripts')
</body>
</html>
