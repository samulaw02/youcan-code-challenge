<?php

namespace App\Console\Commands\Category;

use Illuminate\Console\Command;
use App\Http\Controllers\CLI\CategoryController;
use App\Traits\CommandValidators\BaseValidator;
use App\Traits\TabulateRecord;


class ReadCategory extends Command
{
    use BaseValidator, TabulateRecord;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'category:read {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read a category';

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
        $id = intval($validatedData['id']);
        $category = $this->categoryController->show($id);
        $headers = ['id', 'name', 'created_at', 'updated_at'];
        if (!is_null($category)){
            $this->tabulate($headers, [$category]);
            exit(0);
        }
        $this->error('No data available.');
        exit(0);
    }
}
