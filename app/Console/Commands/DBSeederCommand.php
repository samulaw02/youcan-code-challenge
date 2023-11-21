<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\ProductCategorySeeder;
use App\Console\Commands\Product\IndexProducts;

class DBSeederCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with 1k categories and 1M products';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // Seed categories
            $this->info('Seeding 1k categories...');
            $this->call(CategorySeeder::class);
            $this->info('Done');

            // Seed products
            $this->info('Seeding 1M products...');
            $this->call(ProductSeeder::class);
            $this->info('Done');

            //Attach products to categories (many-to-many relationship)
            $this->info('Attaching products to categories...');
            $this->call(ProductCategorySeeder::class);
            $this->info('Done');


            //Indexing seeded products
            $this->info('Indexing seeded products...');
            $this->call(IndexProducts::class);
            $this->info('Done');

            $this->info('Database seeded successfully.');
        } catch (\Exception $error) {
            $this->info($error->getMessage());
        }

    }
}
