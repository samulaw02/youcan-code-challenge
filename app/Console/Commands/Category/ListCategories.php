<?php

namespace App\Console\Commands\Category;

use Illuminate\Console\Command;
use App\Http\Controllers\CLI\CategoryController;
use App\Traits\CommandValidators\BaseValidator;
use App\Traits\TabulateRecord;


class ListCategories extends Command
{
    use BaseValidator, TabulateRecord;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:list {page?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all categories';

    protected $categoryController;


    public function __construct(CategoryController $categoryController)
    {
        parent::__construct();
        $this->categoryController = $categoryController;
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
        $categories = $this->categoryController->index($page);
        $headers = ['id', 'name'];
        if (!empty($categories)){
            $this->tabulate($headers, $categories);
            exit(0);
        }
        $this->error('No data available.');
        exit(0);
    }
}
