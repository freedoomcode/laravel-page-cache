<?php
namespace PageCache;

use Illuminate\Support\ServiceProvider;
use PageCache\Middleware\HtmlCache;

class PageCacheServiceProvider extends ServiceProvider
{
    protected $commands = [

    ];

    protected $routeMiddleware = [
        'page_cache' => HtmlCache::class,
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // $this->commands($this->commands);

        $configPath = __DIR__ . '/../config/page_cache.php';
        $this->mergeConfigFrom($configPath, 'page_cache');

        $this->registerRouteMiddleware();

        $routeConfig = [
            'middleware' => ['page_cache'],
        ];

        $this->getRouter()->group($routeConfig, function ($router) {
            $router->get('page-demo', function () {
                return '<h2>this page is cached.</h2>';
            });
        });
    }

    public function boot()
    {
        $configPath = __DIR__ . '/../config/page_cache.php';
        $this->publishes([$configPath => $this->getConfigPath()], 'config');
    }

    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }
    }

    protected function getRouter()
    {
        return $this->app['router'];
    }
}
