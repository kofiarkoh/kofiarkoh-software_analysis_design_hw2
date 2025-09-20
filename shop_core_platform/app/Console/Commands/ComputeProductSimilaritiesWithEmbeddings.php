<?php


namespace App\Console\Commands;

use App\Models\Vendor\Product;
use Illuminate\Console\Command;
use App\Jobs\RecomputeSimilarProducts;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ComputeProductSimilaritiesWithEmbeddings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example:
     *   php artisan embeddings:compute --ids=1,2,3
     */
    protected $signature = 'embeddings:compute {--ids= : Comma-separated list of product IDs}';

    /**
     * The console command description.
     */
    protected $description = 'Queue ML embedding jobs for selected products';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $idsOption = $this->option('ids');
        $query = Product::query()
            ->where('status', Product::STATUS_PUBLISHED)
            ->where('quantity', '>', 0);

        if ($idsOption) {
            $ids = array_filter(explode(',', $idsOption));
            $query->whereIn('id', $ids);
        }

        $products = $query->get();
        $count = 0;

        foreach ($products as $product) {
            RecomputeSimilarProducts::dispatch($product->getKey());
            $count++;
        }

        $this->info("Queued {$count} product(s) for ML embedding computation.");

        return CommandAlias::SUCCESS;
    }
}
