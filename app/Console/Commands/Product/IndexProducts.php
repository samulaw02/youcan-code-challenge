<?php

namespace App\Console\Commands\Product;

use Illuminate\Console\Command;
use App\Services\ProductService;
use App\Services\ElasticSearchService;


class IndexProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index existing products';

    protected $productService;

    protected $elasticsearchService;


    /**
     * The constructor.
     *
     *
     */
    public function __construct(ProductService $productService, ElasticSearchService $elasticsearchService)
    {
        parent::__construct();
        $this->productService = $productService;
        $this->elasticsearchService = $elasticsearchService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
            $batchSize = 1000;
            $offset = 0;

            do {
                $products = $this->productService->getProductsBatch($batchSize, $offset);

                if ($products->isEmpty()) {
                    break;
                }
                // Indexing products into Elasticsearch
                $this->elasticsearchService->bulkIndex($products);
                $offset += $batchSize;
            } while (true);

            $this->info('Products indexing completed!');
        } catch(\Exception $error) {
            $this->info($error);
        }

    }
}
