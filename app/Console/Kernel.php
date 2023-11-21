<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\Product\{
    CreateProduct,
    DeleteProduct,
    ListProducts,
    ReadProduct,
    UpdateProduct,
    IndexProducts
};
use App\Console\Commands\Category\{
    CreateCategory,
    DeleteCategory,
    ListCategories,
    ReadCategory,
    UpdateCategory
};

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CreateProduct::class,
        DeleteProduct::class,
        ListProducts::class,
        ReadProduct::class,
        UpdateProduct::class,
        IndexProducts::class,
        CreateCategory::class,
        DeleteCategory::class,
        ListCategories::class,
        ReadCategory::class,
        UpdateCategory::class
    ];


    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
