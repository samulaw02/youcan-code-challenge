<?php

namespace App\Console\Commands\Category;

use Illuminate\Console\Command;
use App\Http\Controllers\CLI\CategoryController;
use App\Traits\CommandValidators\BaseValidator;


class DeleteCategory extends Command
{
    use BaseValidator;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:delete {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a category';


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
            'id' => ['required', 'exists:categories,id']
        ];
        $validatedData = $this->validateArguments($this->arguments(), $rules);
        $category = $this->categoryController->destroy($validatedData['id']);
        if ($category) {
            $this->info('Category deleted successfully');
            exit(0);
        } else {
            $this->error('Category not found');
            exit(0);
        }
    }
}
