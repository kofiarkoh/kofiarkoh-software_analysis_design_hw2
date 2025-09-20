<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\SearchQuery;
use App\Models\Shop;
use App\Models\Vendor\Product;
use App\Models\Vendor\ShopPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Meilisearch\Client;
use Spatie\QueryBuilder\QueryBuilder;
use Meilisearch\Contracts\FacetSearchQuery;

class ProductController extends Controller
{
    public function suggestions(Request $request)
    {
        $q = trim((string) $request->query('query', ''));
        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        /** @var Client $client */
        $client   = app(Client::class);
        $indexUid = (new Product)->searchableAs(); // e.g., "products"
        $index    = $client->index($indexUid);

        // Optional availability filter (keep if you want)
        $baseFilter = 'status = "published" AND quantity > 0';

        // Pull a small batch of hits that match the user’s prefix
        $hits = $index->search($q, [
            'filter' => [$baseFilter],                 // remove if you don't need it
            'attributesToRetrieve' => ['name','description'],
            'limit' => 50,                             // overfetch a bit to mine tokens
        ]);

        $rawHits = is_object($hits) && method_exists($hits, 'getHits')
            ? $hits->getHits()
            : (is_array($hits) ? ($hits['hits'] ?? []) : []);

        // Tokenize name + description, pick tokens that start with the query prefix
        $stop = collect(['the','and','for','with','a','an','of','to','in','on','by','or','&']);
        $norm = fn(string $s) => mb_strtolower(trim($s));

        $suggestions = collect([$q]); // always include raw query first

        foreach ($rawHits as $h) {
            $texts = array_filter([ $h['name'] ?? '', $h['description'] ?? '' ]);

            foreach ($texts as $text) {
                // tokens: letters/digits/hyphens
                preg_match_all('/[A-Za-z0-9][A-Za-z0-9\-]*/u', $text, $m);
                $tokens = $m[0] ?? [];

                $count = count($tokens);
                for ($i = 0; $i < $count; $i++) {
                    $tok = $tokens[$i];

                    if (!Str::startsWith(Str::lower($tok), Str::lower($q))) {
                        continue;
                    }

                    // single token suggestion (e.g., "lenovo")
                    $suggestions->push($tok);

                    // two-word combo (e.g., "lenovo thinkpad")
                    if ($i + 1 < $count) {
                        $next = $tokens[$i + 1];
                        if (!$stop->contains($norm($next))) {
                            $suggestions->push($tok . ' ' . $next);
                        }
                    }
                }
            }
        }

        // Optional: playful double-last-char like "lenovoo"
        if (mb_strlen($q) >= 3) {
            $last = mb_substr($q, -1);
            $suggestions->push($q . $last);
        }

        // Clean, dedupe (case-insensitive), cap to 10 → [{label: "..."}]
        $out = $suggestions
            ->map(fn ($s) => trim(preg_replace('/\s+/', ' ', $s)))
            ->filter(fn ($s) => mb_strlen($s) >= 2)
            ->unique(fn ($s) => mb_strtolower($s))
            ->take(10)
            ->values()
            ->map(fn ($label) => ['label' => $label]);

        return response()->json($out);
    }



    public function index(Request $request)
    {


        $query = $request->getQueryString();

        $q = trim(
            $request->input('q')
            ?? $request->input('filter.name')
            ?? $request->input('filter_name')
            ?? ''
        );



        if (auth('web')->check() && $query) {
            SearchQuery::create([
                'user_id'      => auth('web')->user()->id,
                'search_term' => $query,
            ]);
        }

        Shop::clearShopInformationFromSession();


        $filters = ['status = published', 'quantity > 0'];

        $products = Product::search($q)
            ->options(['filter' => $filters]) // works for Meilisearch/Algolia; ignored by database driver
            ->paginate(24); // returns LengthAwarePaginator

        if ($request->filled('reels')) {
            switch ($request->input('reels')) {
                case 'new-arrivals':
                    $products = $products->newArrivals();
                    break;
            }
        }

        return view('products.index', compact('products'));
    }


    private function renderProductDetail(Product $product)
    {
        $product->load('variants.attributeValues.attribute');

        $cart = auth()->user()?->activeCart;

        $variantQuantities = collect();

        if ($product->variants->isNotEmpty()) {
            $variantQuantities = $cart?->items()
                ->whereIn('product_variant_id', $product->variants->pluck('id'))
                ->get()
                ->keyBy('product_variant_id');

        }

        $cartItem = $product->variants->isEmpty()
            ? $cart?->items()->where('product_id', $product->id)->whereNull('product_variant_id')->first()
            : null;

        $photoUrls = collect($product->photos)
            ->map(fn ($photo) => Storage::disk('public')->url($photo));

        $similar = $product->similarFromBaseFilter(limit: 5);

        $sections = [
            [
                'key'     => 'similar-products',
                'title'   => 'Similar Products',
                'link'    => route('products.index', ['reels' => 'new-arrivals']),
                'items'   => $similar,
            ],
        ];

        return view('products.detail', [
            'product' => $product,
            'cartItem' => $cartItem,
            'variantQuantities' => $variantQuantities,
            'photoUrls' => $photoUrls,
            'sections' => $sections,
        ]);
    }
    public function show(Product $product)
    {
       return $this->renderProductDetail($product);
    }


    public function indexByShop(Request $request, $shopSlug)
    {
        Shop::setShopInformationInSession($shopSlug);

        $query = $request->getQueryString();

        if (auth('web')->check() && $query) {
            SearchQuery::create([
                'user_id'      => auth('web')->user()->id,
                'search_term' => $query,
            ]);
        }
        $shop = Shop::where('slug', $shopSlug)->firstOrFail();

        $products = Product::filteredPaginated(shopId: $shop->id);

        return view('products.index', compact('products', 'shop'));
    }


    public function showForShop($shopSlug, $productId)
    {
        Shop::setShopInformationInSession($shopSlug);

        $product = Product::where('shop_id', session('shop_id'))->where('id', $productId)->firstOrFail();

        return $this->renderProductDetail($product);
    }

}
