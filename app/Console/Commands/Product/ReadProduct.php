<?php

namespace App\Console\Commands\Product;

use Illuminate\Console\Command;
use App\Http\Controllers\CLI\ProductController;
use App\Traits\CommandValidators\BaseValidator;
use App\Traits\TabulateRecord;


class ReadProduct extends Command
{
    use BaseValidator, TabulateRecord;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:read {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read a product';

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
        $id = intval($validatedData['id']);
        $product = $this->productController->show($id);
        $headers = ['id', 'name', 'description', 'price', 'image', 'created_at', 'updated_at'];
        if (!is_null($product)){
            $this->tabulate($headers, [$product]);
            exit(0);
        }
        $this->error('No data available.');
        exit(0);
    }
}
