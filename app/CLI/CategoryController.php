<?php

namespace App\Http\Controllers\CLI;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;


class CategoryController extends Controller
{
    protected $categoryService;

    /**
     * Constructor.
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($page)
    {
        $data = ['page' => $page];
        return $this->categoryService->getAllCategoriesPaginated($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($productData)
    {
        return $this->categoryService->createCategory($productData);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return $this->categoryService->getCategoryById($id);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(array $data, $id)
    {
        $product = $this->categoryService->getCategoryById($id);
        if ($product){
            return $this->categoryService->updateCategory($product, $data);
        }
        return null;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->categoryService->deleteCategory($id);
    }
}
