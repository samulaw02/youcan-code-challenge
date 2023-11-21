<?php

namespace App\Console\Commands\Product;

use Illuminate\Console\Command;
use App\Traits\CommandValidators\Product\CreateProductValidator;
use App\Http\Controllers\CLI\ProductController;
use Exception;


class CreateProduct extends Command
{
    use CreateProductValidator;

    protected $signature = 'product:create {category_ids*}';

    protected $description = 'Create a new product';

    protected $productController;

    public function __construct(ProductController $productController)
    {
        parent::__construct();
        $this->productController = $productController;
    }


    public function handle()
    {
        try {
            $rules = [
                'category_ids' => ['nullable', 'array'],
                'category_ids.*' => ['exists:categories,id']
            ];
            $arguments = $this->validateArguments($this->arguments(), $rules);
            $validatedInput = $this->promptAndValidate();
            $productData = array_merge($validatedInput, $arguments);
            $product = $this->productController->store($productData);
            if ($product) {
                $this->info('Product created successfully. ID: ' . $product->id);
                exit(0);
            }
        } catch (\Exception $e) {
            $this->error('Validation error: ' . $e->getMessage());
            exit(0);
        }
    }
}
