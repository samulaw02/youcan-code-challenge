<?php

namespace App\Console\Commands\Product;

use Illuminate\Console\Command;
use App\Http\Controllers\CLI\ProductController;
use App\Traits\CommandValidators\BaseValidator;


class DeleteProduct extends Command
{
    use BaseValidator;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:delete {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a product';


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
        $rules = [
            'id' => ['required', 'exists:products,id']
        ];
        $validatedData = $this->validateArguments($this->arguments(), $rules);
        $product = $this->productController->destroy($validatedData['id']);
        if ($product) {
            $this->info('Product deleted successfully');
            exit(0);
        } else {
            $this->error('Product not found');
            exit(0);
        }
    }
}
