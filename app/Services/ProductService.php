<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Cache\CacheManager;
use App\Models\Product;


class ProductService
{
    protected $productRepository;
    protected $cacheManager;
    protected $cacheDuration = 3;
    protected $commonCacheTag = 'products';


    public function __construct(ProductRepository $productRepository, CacheManager $cacheManager)
    {
        $this->productRepository = $productRepository;
        $this->cacheManager = $cacheManager;
    }

    public function getAllProducts()
    {
        return $this->productRepository->getAllWithCategories();
    }

    public function getAllProductsPaginated(array $data=[])
    {
        $page = $data['page'] ?? 1;
        $perPage = 10;
        $cacheKey = $this->generateCacheKey($data, $page);
        return $this->cacheManager->tags([$this->commonCacheTag])->remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use($data, $perPage, $page) {
            return $this->productRepository->paginate($data, $perPage, $page);
        });
    }


    public function getProductById($id)
    {
        //Generate a unique cache key based on the product ID
        $cacheKey = "product_{$id}_details";
        return $this->cacheManager->tags([$this->commonCacheTag])->remember($cacheKey, now()->addMinutes($this->cacheDuration), function () use($id) {
            return $this->productRepository->find($id);
        });
    }

    public function createProduct(array $data)
    {
        $product = $this->productRepository->create($data);
        $this->cacheManager->tags([$this->commonCacheTag])->flush();
        return $product;
    }

    public function updateProduct(Product $product, array $data)
    {
        $updatedProduct = $this->productRepository->update($product, $data);
        // Generate a unique cache key based on the product ID
        $cacheKey = "product_{$updatedProduct->id}_details";
        $this->cacheManager->tags([$this->commonCacheTag])->put($cacheKey, $updatedProduct, now()->addMinutes($this->cacheDuration));
        return $updatedProduct;
    }

    public function deleteProduct($id)
    {
        $this->cacheManager->tags([$this->commonCacheTag])->flush();
        return $this->productRepository->delete($id);

    }


    public function generateCacheKey($data, $page)
    {
        $searchGlobal = $data['search_global'] ?? '';
        $sortColumn = $data['sort_column'] ?? 'created_at';
        $sortDirection = $data['sort_direction'] ?? 'desc';
        return "products_{$page}_{$searchGlobal}_{$sortColumn}_{$sortDirection}";
    }


    public function seedProducts(array $data)
    {
        return $this->productRepository->insert( $data);
    }


    public function getProductsBatch($batchSize, $offset)
    {
       return $this->productRepository->getProductsBatch($batchSize, $offset);
    }

    public function searchProducts(array $data)
    {
        return $this->productRepository->searchProducts($data);
    }
}
