<?php

namespace App\Http\Controllers\CLI;

use App\Http\Controllers\Controller;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;

    /**
     * Constructor.
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($page)
    {
        $data = ['page' => $page];
        return $this->productService->getAllProductsPaginated($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($productData)
    {
        return $this->productService->createProduct($productData);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->productService->getProductById($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(array $data, $id)
    {
        $product = $this->productService->getProductById($id);
        if ($product){
            return $this->productService->updateProduct($product, $data);
        }
        return null;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->productService->deleteProduct($id);
    }
}
