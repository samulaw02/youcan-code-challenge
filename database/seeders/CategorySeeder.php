<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\SeederService;
use App\Services\CategoryService;


class CategorySeeder extends Seeder
{
    /**
     * @var SeederService
     */
    private $seederService;

    /**
     * @var CategoryService;
     */
    private $categoryService;


    /**
     * ProductSeeder constructor.
     *
     * @param SeederService $seederService
     */
    public function __construct(SeederService $seederService, CategoryService $categoryService)
    {
        $this->seederService = $seederService;
        $this->categoryService = $categoryService;

    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $totalRecords = 1000;
        $batchSize = 500;
        $data = array();
        for ($i = 0; $i < $totalRecords; $i++) {
            $data[] = $this->seederService->generateCategory($i);
        };
        $chunkData = array_chunk($data, $batchSize);
        foreach ($chunkData as $data) {
            $this->categoryService->seedCategories($data);
        }
    }
}
