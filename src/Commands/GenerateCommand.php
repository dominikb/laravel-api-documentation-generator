<?php


namespace Dominikb\LaravelApiDocumentationGenerator\Commands;


use Dominikb\LaravelApiDocumentationGenerator\HtmlFormatter;
use Dominikb\LaravelApiDocumentationGenerator\RouteParser;
use Dominikb\LaravelApiDocumentationGenerator\TextFormatter;
use Illuminate\Console\Command;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\File;

class GenerateCommand extends Command
{
    protected $signature = 'documentation:generate';

    public function handle()
    {
        /** @var Router $router */
        $router = app(Router::class);
        $parser = new RouteParser($router, new TextFormatter);

        $formatted = $parser->format();

        File::put(storage_path('documentation.txt'), $formatted);

        $this->info('Success!');
    }
}