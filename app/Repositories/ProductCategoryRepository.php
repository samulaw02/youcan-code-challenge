<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Category;


class ProductCategoryRepository 
{
    protected $product;
    protected $category;


    public function __construct(Product $product, Category $category)
    {
        $this->product = $product;
        $this->category = $category;
    }

    public function attachProductsToCategories()
    {
        $products = $this->product->all();
        $categories = $this->category->all();
        $products->each(function ($product) use ($categories) {
            $product->categories()->attach(
                $categories->random(rand(1, 5))->pluck('id')->toArray()
            );
        });
        return $products;
    }


}