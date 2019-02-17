<?php


namespace Dominikb\LaravelApiDocumentationGenerator\Commands;


use Dominikb\LaravelApiDocumentationGenerator\Route;
use Dominikb\LaravelApiDocumentationGenerator\RouteParameter;
use Dominikb\LaravelApiDocumentationGenerator\RouteParser;
use Dominikb\LaravelApiDocumentationGenerator\TextFormatter;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Output\BufferedOutput;

class GenerateCommand extends Command
{
    protected $signature = 'documentation:generate';

    public function handle()
    {
        Artisan::call('route:list');

        $parser = app(RouteParser::class);

        $collection = $parser->parse(Artisan::output());

        $formatted = $parser->format();

        File::put(storage_path('documentation.txt'), $formatted);

        $this->info('Success!');
    }
}