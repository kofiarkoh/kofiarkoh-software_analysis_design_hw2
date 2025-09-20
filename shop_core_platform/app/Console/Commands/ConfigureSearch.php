<?php

namespace App\Console\Commands;

// app/Console/Commands/ConfigureSearch.php
use Meilisearch\Client;
use Illuminate\Console\Command;

class ConfigureSearch extends Command {
    protected $signature = 'search:configure';
    public function handle() {
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $client->index('products');

        $index->updateSettings([
            'searchableAttributes' => ['name','description'],
            'filterableAttributes' => ['status','quantity','price'],
            'sortableAttributes'   => ['price'],
            'typoTolerance'        => ['enabled' => true, 'minWordSizeForTypos' => ['oneTypo'=>4,'twoTypos'=>8]],
            'synonyms'             => [
                'shoes'    => ['sneakers','trainers','footwear'],
                't-shirt'  => ['tee','shirt'],
            ],
            // Optional custom ranking: prioritize name matches and popularity
            'rankingRules' => [
                'words','typo','proximity','attribute','sort','exactness',
              //  'desc(popularity)' // add a popularity column if you have it
            ],
        ]);
        $this->info('Search configured.');
    }
}

