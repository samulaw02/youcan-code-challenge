<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Services\JSONResponseService;
use App\Http\Requests\Product\CreateProductRequest;
use App\Services\FileManager;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\ElasticSearchService;
use App\Traits\ProductMixin;



class ProductController extends Controller
{
    use ProductMixin;

    protected $productService;
    protected $jsonResponseService;
    protected $fileManager;
    protected $elasticSearchService;

    /**
     * Constructor.
     */
    public function __construct(ProductService $productService, JSONResponseService $jsonResponseService, FileManager $fileManager, ElasticSearchService $elasticSearchService)
    {
        $this->productService = $productService;
        $this->jsonResponseService = $jsonResponseService;
        $this->fileManager = $fileManager;
        $this->elasticSearchService = $elasticSearchService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($this->isSearchable($request))
        {
            $products = $this->elasticSearchService->searchProducts($request->all());
        }
        else {
            $products = $this->productService->getAllProductsPaginated($request->all());
        }
        return $this->jsonResponseService->success('Product fetched successfully', $products, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $uploadImage = $this->fileManager->uploadFile($data['image']);
                if ($uploadImage) {
                    $data['image'] = $uploadImage;
                }
            }
            $product = $this->productService->createProduct($data);
            if ($product) {
                return $this->jsonResponseService->success('Product created successfully', $product, 201);
            }
            return $this->jsonResponseService->failure('Product creation failed');
        } catch (\Exception $error) {
            return $this->jsonResponseService->error($error->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        if ($product){
            return $this->jsonResponseService->success('Product fetched successfully', $product, 200);
        }
        return $this->jsonResponseService->failure('Product not found', 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try{
            $data = $request->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $this->fileManager->uploadFile($data['image']);
            }
            $updatedProduct = $this->productService->updateProduct($product, $data);
            return $this->jsonResponseService->success('Product updated successfully', $updatedProduct, 200);
        } catch (\Exception $error) {
            return $this->jsonResponseService->error($error->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $isDeleted = $this->productService->deleteProduct($id);
        if ($isDeleted) {
            return $this->jsonResponseService->success('Product deleted successfully', 200);
        }
        return $this->jsonResponseService->failure('Product not found', 404);
    }
}
