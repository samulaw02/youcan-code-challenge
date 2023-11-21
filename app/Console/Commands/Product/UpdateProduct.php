<?php

namespace App\Console\Commands\Product;

use Illuminate\Console\Command;
use App\Http\Controllers\CLI\ProductController;
use App\Traits\CommandValidators\Product\UpdateProductValidator;


class UpdateProduct extends Command
{
    use UpdateProductValidator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update {id} {--category_ids=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a product';

    protected $productController;

    public function __construct(ProductController $productController)
    {
        parent::__construct();
        $this->productController = $productController;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            [$arguments, $options] = $this->validateArgumentsAndOptions([]);
            $validatedInput = $this->validateSelect();
            $productData = empty($options['category_ids']) ? $validatedInput : array_merge($validatedInput, $options);
            $updatedProduct = $this->productController->update($productData, intval($arguments));
            if ($updatedProduct)
            {
                $this->info('Product updated successfully');
                $headers = ['id', 'name', 'description', 'price', 'image', 'created_at', 'updated_at'];
                $this->tabulate($headers, [$updatedProduct]);
                exit(0);
            }
            $this->error('Product not found');
            exit(0);
        } catch(\Exception $e) {
            $this->error('Unable to create product created due to ' . $e->getMessage());
        }
    }
}
