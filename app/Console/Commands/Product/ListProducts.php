<?php

namespace App\Console\Commands\Product;

use Illuminate\Console\Command;
use App\Http\Controllers\CLI\ProductController;
use App\Traits\CommandValidators\BaseValidator;
use App\Traits\TabulateRecord;


class ListProducts extends Command
{
    use BaseValidator, TabulateRecord;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:list {page?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all products';

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
            'page' => ['nullable','regex:/[0-9]/']
        ];
        $validatedData = $this->validateArguments($this->arguments(), $rules);
        $page = intval($validatedData['page']) ?? 1;
        $products = $this->productController->index($page);
        $headers = ['id', 'name', 'description', 'price', 'image', 'created_at', 'updated_at'];
        if (!empty($products)){
            $this->tabulate($headers, $products);
            exit(0);
        }
        $this->error('No data available.');
        exit(0);
    }
}
