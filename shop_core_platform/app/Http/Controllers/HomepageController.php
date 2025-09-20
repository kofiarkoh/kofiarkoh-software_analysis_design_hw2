<?php

namespace App\Http\Controllers;

use App\Models\HomepageBanner;
use App\Models\Shop;
use App\Models\Vendor\Category;
use App\Models\Vendor\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomepageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function home(Request $request)
    {
        Shop::clearShopInformationFromSession();

        $banners = HomepageBanner::getBanners();

        $products = Product::filteredPaginated(
            perPage: 5
        );

        $categories = Category::take(9)->get();

        $sections = [
            [
                'key'     => 'new-arrivals',
                'title'   => 'New Arrivals',
                'link'    => route('products.index', ['reels' => 'new-arrivals']),
                'items'   => Product::baseFilter()->take(5)->get(),
            ],
        ];
        return view('welcome', compact('products', 'categories', 'banners', 'sections'));
    }


    public function shopHomepage(Request $request, $shopId): object
    {
        Shop::setShopInformationInSession($shopId);

        $products = Product::filteredPaginated(
            perPage: 5,
            shopId: session('shop_id'),
        );

        $banners = HomepageBanner::getBanners();
        $categories = Category::take(9)->get();

        $shop = $shopId;

        $sections = [];
        return view('welcome', compact('products', 'categories', 'shop', 'banners', 'sections'));
    }
}
