<?php

namespace App\Console\Commands\Category;

use Illuminate\Console\Command;
use App\Http\Controllers\CLI\CategoryController;
use App\Traits\CommandValidators\BaseValidator;
use App\Traits\TabulateRecord;


class UpdateCategory extends Command
{
    use BaseValidator, TabulateRecord;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:update {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update a category';

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
        try {
            $rules = [
                'id' => ['required', 'exists:categories,id']
            ];
            $argument = $this->validateArguments($this->arguments(), $rules);
            $name = ['name' => $this->askAndValidate('Enter Category Name', 'name', 'string|string|unique:categories,name')];
            $updatedCategory = $this->categoryController->update($name, intval($argument['id']));
            if ($updatedCategory) {
                $this->info('Category updated successfully');
                $headers = ['id', 'name', 'created_at', 'updated_at'];
                $this->tabulate($headers, [$updatedCategory]);
                exit(0);
            }
            $this->error('Category not found');
            exit(0);
        } catch (\Exception $e) {
            $this->error('Validation error: ' . $e->getMessage());
            exit(0);
        }
    }
}
