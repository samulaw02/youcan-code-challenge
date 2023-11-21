<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\JSONResponseService;
use App\Services\CategoryService;
use App\Http\Requests\Category\CreateCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $jsonResponseService;

    public function __construct(CategoryService $categoryService, JSONResponseService $jsonResponseService)
    {
        $this->categoryService = $categoryService;
        $this->jsonResponseService = $jsonResponseService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = $this->categoryService->getAllCategoriesPaginated($request->all());
        return $this->jsonResponseService->success('Categories fetched successfully', $categories, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $category = $this->categoryService->createCategory($data);
            if ($category) {
                return $this->jsonResponseService->success('Category created successfully', $category, 201);
            }
            return $this->jsonResponseService->failure('Category creation failed');
        } catch (\Exception $error) {
            return $this->jsonResponseService->error($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        if ($category) {
            return $this->jsonResponseService->success('Category fetched successfully', $category, 200);
        }
        return $this->jsonResponseService->failure('Category not found', 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryService->getCategoryById($id);
        if ($category){
            $data = $request->validated();
            $updatedProduct = $this->categoryService->updateCategory($category, $data);
            return $this->jsonResponseService->success('Category updated successfully', $updatedProduct, 200);
        }
        return $this->jsonResponseService->failure('Category not found', 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $isDeleted = $this->categoryService->deleteCategory($id);
        if ($isDeleted) {
            return $this->jsonResponseService->success('Category deleted successfully', 200);
        }
        return $this->jsonResponseService->failure('Category not found', 404);
    }
}
