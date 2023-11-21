<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SeederService;
use App\Services\ProductService;


class ProductSeeder extends Seeder
{
    /**
     * @var SeederService
     */
    private $seederService;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * ProductSeeder constructor.
     *
     * @param SeederService $seederService
     */
    public function __construct(SeederService $seederService, ProductService $productService)
    {
        $this->seederService = $seederService;
        $this->productService = $productService;
    }


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $totalRecords = 10000;
        $batchSize = 1000;
        $data = array();
        for ($i = 0; $i < $totalRecords; $i++) {
            $data[] = $this->seederService->generateRandomProduct();
        };
        $chunkData = array_chunk($data, $batchSize);
        foreach ($chunkData as $data) {
            $this->productService->seedProducts($data);
        }
    }
}
