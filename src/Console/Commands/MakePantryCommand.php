<?php

namespace Rmitesh\LaravelPantry\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class MakePantryCommand extends Command
{
    use Concerns\CanValidateInput, Concerns\CanManipulateFiles;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
        make:pantry {pantry?} {--model= :  Generate pantry class for given model}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new pantry class.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("
                   _              
 _ __   __ _ _ __ | |_ _ __ _   _ 
| '_ \ / _` | '_ \| __| '__| | | |
| |_) | (_| | | | | |_| |  | |_| |
| .__/ \__,_|_| |_|\__|_|   \__, |
|_|                         |___/ 

        ");
        
        $pantryPath = app_path('Pantries/');
        $pantryNamespace = 'App\\Pantries';
        $modelNamespace = 'App\\Models';

        // Pantry Name
        $pantry = (string) Str::of($this->argument('pantry') ?? $this->askRequired('Pantry (e.g. `FoodPantry`)', 'pantry'))
            ->studly()
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\');

        if (! Str::of($pantry)->endsWith('Pantry')) {
            $pantry .= 'Pantry';
        }

        list($className, $namespace) = $this->getClassName($pantry);

        $pantry = $className;
        $pantryPath = (string) Str::of($pantryPath)
            ->append($namespace)
            ->trim('\\')
            ->replace('/', '\\')
            ->append($namespace ? '/' : '');

        $pantryNamespace .= $namespace ? "\\$namespace" : '';

        $path = (string) Str::of($pantry)
            ->replace('\\', '/')
            ->prepend($pantryPath)
            ->trim('/')
            ->replace('\\', '/')
            ->append('.php');

        // Model Name
        $model = (string) Str::of($className)->replace('Pantry', '');
        if ( $this->option('model') ) {
            $model = (string) Str::of($this->option('model') ?? $this->askRequired('Model (e.g. `Food`)', 'model'))
                ->studly()
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');
        }

        list($className, $namespace) = $this->getClassName($model);

        $modelNamespace .= !empty($namespace) ? "\\$namespace\\$model" : "\\$model";
        
        if ($this->checkForCollision([ $path ])) {
            return static::INVALID;
        }

        $this->copyStubToApp('Pantry', $path, [
            'className' => $pantry,
            'namespace' => $pantryNamespace,
            'model' => $model,
            'modelNamespace' => $modelNamespace,
        ]);

        $this->info("Panrty [{$path}] created successfully.");

        return static::SUCCESS;
    }

    private function getClassName($name): array
    {
        $name = array_map('ucwords', explode('\\', $name));
        $className = array_pop($name);
        $name = Arr::join($name, '\\');
        $namespace = '';
        if ( $name ) {
            $namespace = (string) Str::of($name)
                ->studly()
                ->trim('/')
                ->trim('\\')
                ->trim(' ')
                ->replace('/', '\\');
        }

        return [ $className, $namespace ];
    }
}
