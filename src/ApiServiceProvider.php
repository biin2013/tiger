<?php

namespace Biin2013\Tiger;

use Biin2013\Tiger\Middleware\ErrorScope;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerMiddleware();
    }

    private function registerMiddleware(): void
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('error-scope', ErrorScope::class);
    }
}