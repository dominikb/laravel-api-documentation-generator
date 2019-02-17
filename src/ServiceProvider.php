<?php namespace Dominikb\LaravelApiDocumentationGenerator;

use Dominikb\LaravelApiDocumentationGenerator\Commands\GenerateCommand;
use Dominikb\LaravelApiDocumentationGenerator\Contracts\RouteFormatter;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleConfigs();
        $this->handleCommands();
        // $this->handleMigrations();
        // $this->handleViews();
        // $this->handleTranslations();
        // $this->handleRoutes();
    }

    private function handleConfigs()
    {
        $configPath = __DIR__ . '/../config/laravel-api-documentation-generator.php';

        $this->publishes([$configPath => config_path('laravel-api-documentation-generator.php')]);

        $this->mergeConfigFrom($configPath, 'laravel-api-documentation-generator');
    }

    private function handleCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateCommand::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->instance(RouteFormatter::class, new TextFormatter);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [RouteParser::class];
    }

    private function handleTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'laravel-api-documentation-generator');
    }

    private function handleViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'laravel-api-documentation-generator');

        $this->publishes([
            __DIR__ . '/../views' => base_path('resources/views/vendor/laravel-api-documentation-generator'),
        ]);
    }

    private function handleMigrations()
    {
        $this->publishes([__DIR__ . '/../migrations' => base_path('database/migrations')]);
    }

    private function handleRoutes()
    {
        include __DIR__ . '/../routes.php';
    }
}
