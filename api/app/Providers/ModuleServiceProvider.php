<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadModuleRoutes();
    }

    /**
     * Load routes from all modules
     */
    protected function loadModuleRoutes(): void
    {
        $modules = [
            'Campaign' => 'Modules\Campaign\routes\api.php',
            'Donation' => 'Modules\Donation\routes\api.php',
        ];

        foreach ($modules as $module => $routeFile) {
            $path = base_path($routeFile);
            if (file_exists($path)) {
                Route::middleware('api')
                    ->group($path);
            }
        }
    }
}
