<?php

namespace App\Console\Commands\Category;

use Illuminate\Console\Command;
use App\Http\Controllers\CLI\CategoryController;
use App\Traits\CommandValidators\BaseValidator;
use Exception;


class CreateCategory extends Command
{

    use BaseValidator;

    protected $signature = 'category:create';

    protected $description = 'Create a new category';

    protected $categoryController;

    public function __construct(CategoryController $categoryController)
    {
        parent::__construct();
        $this->categoryController = $categoryController;
    }
    

    public function handle()
    {
        try {
            $name = ['name' => $this->askAndValidate('Enter Category Name', 'name', 'string|string|unique:categories,name')];
            $category = $this->categoryController->store($name);
            if ($category) {
                $this->info('Category created successfully. ID: ' . $category->id);
                exit(0);
            }
        } catch (\Exception $e) {
            $this->error('Validation error: ' . $e->getMessage());
            exit(0);
        }
    }
}