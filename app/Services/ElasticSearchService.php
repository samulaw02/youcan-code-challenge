<?php

namespace App\Services;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\ProductService;



class ElasticSearchService
{
    private Client $elasticsearchClient;
    private ProductService $fallBackStrategy;


    public function __construct(ProductService $fallBackStrategy)
    {
        $this->initializeElasticsearchClient();
        $this->fallBackStrategy = $fallBackStrategy;
    }

    //initialize elastic search client
    private function initializeElasticsearchClient(): void
    {
        $elasticsearchConfig = config('database.connections.elasticsearch');
        $hosts = [];

        foreach ($elasticsearchConfig['hosts'] as $hostConfig) {
            $host = $hostConfig['scheme'] . '://';

            if (!empty($hostConfig['user']) && !empty($hostConfig['pass'])) {
                $host .= $hostConfig['user'] . ':' . $hostConfig['pass'] . '@';
            }

            $host .= $hostConfig['host'] . ':' . $hostConfig['port'];
            $hosts[] = $host;
        }

        $this->elasticsearchClient = ClientBuilder::create()
            ->setHosts($hosts)
            ->build();
    }


    //bulk index
    public function bulkIndex($models) : void
    {
        $params = ['body' => []];
        foreach ($models as $model) {
            $index = $model->getTable();
            $documentId = $model->getKey();
            $params['body'][] = [
                'index' => [
                    '_index' => $index,
                    '_id' => $documentId,
                ],
            ];
            $data = $model->toArray();
            $data['price'] = (float)$data['price'];
            $params['body'][] = $data;
        }
        try {
            $this->elasticsearchClient->bulk($params);
        } catch (\Exception $e) {
            throw new \Exception("Elasticsearch indexing failed: {$e->getMessage()}");
        }
    }


    //indexing
    public function indexModel(Model $model): void
    {
        $index = $model->getTable();
        $documentId = $model->getKey();
        $data = $model->toArray();
        $data['price'] = (float)$data['price'];
        $params = [
            'index' => $index,
            'id' => $documentId,
            'body' => $data
        ];
        try {
            $this->elasticsearchClient->index($params);
            Log::info("Model with ID $documentId indexed in $index");
        } catch (\Exception $e) {
            throw new \Exception("Elasticsearch indexing failed: {$e->getMessage()}");
        }
    }

    //update index
    public function updateModel(Model $model): void
    {
        $index = $model->getTable();
        $documentId = $model->getKey();
        $updatedData = $model->toArray();
        $this->elasticsearchClient->update([
            'index' => $index,
            'id' => $documentId,
            'body' => [
                'doc' => $updatedData
            ]
        ]);
        Log::info("Model with ID $documentId updated in $index");
    }

    //delete index
    public function deleteModel($index, $documentId): void
    {
        $this->elasticsearchClient->delete([
            'index' => $index,
            'id' => $documentId,
        ]);
        Log::info("Model with ID $documentId deleted from $index");;
    }


    // search query
    public function searchProducts(array $data)
    {
        try {

            $page = $data['page'] ?? 1;
            $perPage = $data['perPage'] ?? 10;
            $searchTerm = $data['search_global'] ?? null;
            $sortBy = $data['sort_column'] ?? 'created_at';
            $sortOrder = $data['sort_direction'] ?? 'desc';
            $params = [
                'index' => 'products',
                'body' => [
                    'from' => ($page - 1) * $perPage,
                    'size' => $perPage,
                    'sort' => [
                        [
                            $sortBy => [
                                'order' => $sortOrder,
                            ],
                        ],
                    ],
                ],
            ];
            //Adding global filter if provided
            if (!is_null($searchTerm)){
                $params['body']['query'] =[
                    'multi_match' => [
                        'fields' => ['name', 'description', 'categories.name'],
                        'query' => $searchTerm,
                        'type' => 'phrase'
                    ],
                ];
            }
            $items = $this->elasticsearchClient->search($params);
            $products = $this->serializeElasticSearchResponse($items['hits']['hits']);
            $totalProducts = $items['hits']['total']['value'] ?? 0;
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            // Create a paginator instance
            $paginator = new LengthAwarePaginator(
                $products,
                $totalProducts,
                $perPage,
                $currentPage,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => 'page',
                ]
            );
            return $paginator;
        } catch (\Exception $e) {
            Log::debug("Elasticsearch search failed: {$e->getMessage()}");
            //Fallback strategy to primary data source
            return $this->fallBackStrategy->searchProducts($data);
        }
    }


    //serialize query response
    private function serializeElasticSearchResponse($items)
    {
        return array_map(function ($item) {
            return $item['_source'];
        }, $items);
    }
}
