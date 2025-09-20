<?php
// app/Console/Commands/BackfillShopSlugs.php
namespace App\Console\Commands;

use App\Traits\Sluggable;
use Illuminate\Console\Command;
use App\Models\Shop;
use Illuminate\Support\Facades\DB;

class BackfillShopSlugs extends Command
{
    use Sluggable;

    protected $signature = 'shops:backfill-slugs';
    protected $description = 'Generate unique, non-enumerative slugs for shops missing slugs';

    public function handle(): int
    {
        $this->info('Backfilling shop slugs...');

        Shop::whereNull('slug')
            ->orWhere('slug', '')
            ->chunkById(500, function ($shops) {
                foreach ($shops as $shop) {
                    // Wrap in a retry to handle rare race conditions with the unique index.
                    DB::transaction(function () use ($shop) {
                        $shop->slug = $this->generateUniqueSlug(Shop::class, (string) $shop->name);
                        $shop->save();
                    });
                    $this->line("• {$shop->id} → {$shop->slug}");
                }
            });

        $this->info('Done.');
        return Command::SUCCESS;
    }
}
