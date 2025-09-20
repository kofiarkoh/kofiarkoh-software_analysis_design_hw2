<x-layout>
    <x-slot:title>
        Home
    </x-slot>

    @section('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />

    @endsection
    @section('homeCategories')
       <div class="mt-20">

           <div class="menu-department-block relative h-full">
               <div class="menu-department-btn relative flex items-center sm:gap-24 gap-4 h-full w-fit cursor-pointer">
                   <div class="flex items-center gap-3">
                       <i class="ph ph-list text-xl max-sm:text-base text-white"></i>
                       <div class="text-button whitespace-nowrap text-white">Department</div>
                   </div>
                   <i class="ph ph-caret-down text-xl max-sm:text-base text-white"></i>
               </div>
               <div
                   class="sub-menu-department style-marketplace absolute top-[84px] left-0 right-0 px-[26px] py-[5px] bg-surface rounded-xl border border-line open">
                   <x-category-view/>
               </div>
           </div>
       </div>

    @endsection

    @section('homeSlider')
        <div class="slider-block w-full style-marketplace lg:h-[500px] p-2 md:h-[400px] sm:h-[320px] mt-10 lg:mt-0">
            <div class="container flex justify-end h-full w-full rounded-2xl">
                <div class="slider-main lg:pl-5 h-full w-full rounded-2xl">
                    <div class="h-full relative rounded-2xl carousel">
                        @foreach($banners as $banner)
                            <div class="slider-item h-full w-full rounded-2xl flex items-center justify-center">
                                <div class="w-full h-full  rounded-2xl flex items-center justify-center">
                                    <img src="{{ Storage::disk('public')->url($banner->file) }}"
                                         alt="marketplace"
                                         class="w-full object-contain rounded-2xl lg:h-[480px] p-2 md:h-[400px] sm:h-[320px]" />

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>



    @endsection

    <div class="md:pt-[60px] pt-10">
        <div class="container">
            <div class="heading flex items-center justify-between gap-5 flex-wrap">
                <div class="left flex items-center gap-6 gap-y-3 flex-wrap">
                    <div class="heading3">Deals of the week</div>

                </div>
                <a href="{{session()->has('shop_name') ?route("shops.products.index", ['shop' => session('shop_slug')])  : route("products.index") }}" class="text-button pb-1 border-b-2 border-black">View All Deals </a>
            </div>
            <div
                class="list grid xl:grid-cols-5 lg:grid-cols-4 md:grid-cols-3 grid-cols-2 sm:gap-[30px] gap-[20px] md:mt-10 mt-6">
                @foreach ($products as $product)
                    @include('partials.product-card', [
                                '$product' => $product,
                            ])
                @endforeach


            </div>
        </div>
    </div>

    {{-- product rails --}}
    @foreach ($sections as $s)
        @include('partials.product-rail', [
            'title' => $s['title'],
            'link'  => $s['link'] ?? null,
            'items' => $s['items'],
        ])
    @endforeach




    @section('scripts')
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <!-- Slick JS -->
        <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function () {

                $('.carousel').slick({
                    dots: true,
                    infinite: true,
                    speed: 500,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    autoplay: true,
                    autoplaySpeed: 2000
                });

            });
        </script>
    @endsection

</x-layout>
