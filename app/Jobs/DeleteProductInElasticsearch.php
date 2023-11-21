<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ElasticSearchService;


class DeleteProductInElasticsearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $index;
    protected $documentId;

    /**
     * Create a new job instance.
     */
    public function __construct($index, $documentId)
    {
        $this->index = $index;
        $this->documentId = $documentId;
    }

    /**
     * Execute the job.
     */
    public function handle(ElasticSearchService $elasticsearchService): void
    {
        // Index the product into Elasticsearch
        $elasticsearchService->deleteModel($this->index, $this->documentId);
    }
}
