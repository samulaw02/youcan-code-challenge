<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use App\Jobs\IndexProductIntoElasticsearch;
use App\Jobs\UpdateProductInElasticsearch;
use App\Jobs\DeleteProductInElasticsearch;


class ProductRepository
{
    protected $model;

    public function __construct(Product $product)
    {
        $this->model = $product;
    }

    public function getAllWithCategories()
    {
        return $this->model->with('categories')->get();
    }


    public function paginate($data, $perPage, $page)
    {
        $query = $this->model->with('categories');
        $products = $query->orderBy('created_at', 'desc')->paginate($perPage, ['*'], 'page', $page);
        return $products;
    }

    public function find($id)
    {
        return $this->model->with('categories')->find($id);
    }

    public function create(array $data)
    {
        // Extract 'category_ids' from the data (if present)
        $categoryIds = Arr::pull($data, 'category_ids');
        // Create the product
        $product = $this->model->create($data);
        // Attach categories
        if ($categoryIds) {
            $product->categories()->attach($categoryIds);
        }
        $product->load('categories');
        //Index product in elastic search
        IndexProductIntoElasticsearch::dispatch($product);
        return $product;
    }



    public function update(Product $product, array $data)
    {
        // Extract 'category_ids' from the data (if present)
        $categoryIds = Arr::pull($data, 'category_ids');
        $product->update($data);
        if (!is_null($categoryIds)) {
            $product->categories()->sync($categoryIds);
        }
        $product->load('categories');
        //Update product in elastic search
        UpdateProductInElasticsearch::dispatch($product);
        return $product;
    }

    public function delete($id)
    {
        $product = $this->model->find($id);
        if ($product) {
            $product->categories()->detach();
            $product->delete();
            //delete product in elastic search
            DeleteProductInElasticsearch::dispatch('products', $id);
            return true;
        }
        return false;
    }


    public function insert($array)
    {
        return $this->model->insert($array);
    }


    public function getProductsBatch($batchSize, $offset)
    {
        return $this->model->with('categories')->take($batchSize)->skip($offset)->get();
    }


    public function searchProducts(array $data)
    {
        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? 10;
        $searchTerm = $data['search_global'] ?? '';
        $sortBy = $data['sort_column'] ?? 'created_at';
        $sortOrder = $data['sort_direction'] ?? 'desc';
        $query = $this->model->with('categories');
        if ($searchTerm !== '') {
            $query->where(function (Builder $query) use ($searchTerm) {
                $query->whereHas('categories', function (Builder $q) use ($searchTerm) {
                    $q->where('name', $searchTerm);
                })->orWhere('name', 'like', '%' . $searchTerm)
                  ->orWhere('description', 'like', '%' . $searchTerm);
            });
        }
        return $query->orderBy($sortBy, $sortOrder)->paginate($perPage, ['*'], 'page', $page);
    }

}
