<?php

// app/Jobs/RecomputeSimilarProducts.php
namespace App\Jobs;

use App\Models\Vendor\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecomputeSimilarProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;          // retries
    public int $backoff = 30;       // seconds

    public function __construct(private int $productId) {}

    public function handle(): void
    {
        try {

            $product = Product::where('id',$this->productId)->firstOrFail();
            if (!$product) {
                Log::channel('ml')->info( "ML service RETURNING, no product found: ");
                return; }

            $payload = [
                'product_id' => $product->id,
                'shop_id'  => $product->shop_id ?? null, // if multi-tenant
                'title'      => (string) $product->name,
                'description'=> $product->description,
                'price'      => (float)$product->price,
//            'category'   => optional($product->category)->name,
//            'tags'       => $product->tags?->pluck('name')->all() ?? [],
                'top_k'      => 12,
                'write_back' => true,  // let ML service update DB, or false to return IDs
            ];

            Log::channel('ml')->info( "ML service PAYLOAD: " . json_encode($payload));
            $resp = Http::withToken(env('ML_SERVICE_TOKEN'))
                ->timeout(15)
                ->post( env('ML_SERVICE_URL').'/recompute', $payload);

            if ($resp->failed()) {
                // Throwing will trigger retry with backoff

                Log::channel('ml')->info("ML service error: ".$resp->body());
                throw new \RuntimeException("ML service error: ".$resp->body());
            }

            // If ML returns results instead of writing back:
            // $data = $resp->json();
            // save to similar_products...
        }

        catch (\Throwable $e) {
            // log full context, then rethrow so Laravel marks it failed / retries
            Log::channel('ml')->error('RecomputeSimilar crashed', [
                'productId' => $this->productId,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            throw $e;
        }

    }

    // Called AFTER final attempt fails
    public function failed(\Throwable $e): void
    {
        Log::channel('ml')->error('RecomputeSimilar FAILED after all retries', [
            'productId' => $this->productId,
            'error'     => $e->getMessage(),
            'trace'     => $e->getTraceAsString(),
        ]);
    }
}
