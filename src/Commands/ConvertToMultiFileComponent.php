<?php

namespace LivewireV4\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use LivewireV4\Converter\ConversionManager;
use LivewireV4\Utility\DirecoryListing;
use LivewireV4\Utility\FormatFile;
use LivewireV4\Utility\RenderedViewContent;
use LivewireV4\Utility\ViewFilePath;

class ConvertToMultiFileComponent extends Command
{
    protected $signature = 'convert-class-to:mfc {path}';

    protected $description = 'Convert Livewire class based component to Multi File Component(MFC)';

    public function handle(): void
    {
        $path = $this->argument('path');
        $fullPath = config('livewire-v4-patch.class_component_path').DIRECTORY_SEPARATOR.$path;

        if(! File::exists($fullPath)) {
            $this->error("$fullPath Does Not exists");
            return;
        }

        if (File::isFile($fullPath)) {
            $this->convertToMFC($fullPath);
        } else {
            $files = DirecoryListing::make()->path($fullPath)->fileListings();
            $this->info('Total '.count($files).' to Convert');
            foreach ($files as $file) {
                $this->convertToMFC($file);
            }
        }
    }

    public function convertToMFC($fullPath)
    {
        $mfcComponentPath = config('livewire-v4-patch.mfc_component_path');

        $emoji = match(true){
            config('livewire-v4-patch.emoji') => '⚡',
            default => ''
        };

        $componentDirectory = str()->of(dirname($this->argument('path')))->lower();
        $convertedComponentName = str()->of(File::name($fullPath))->kebab()->toString();
        $convertedComponentPath = Arr::join(
            array : [
                $mfcComponentPath,
                $componentDirectory,
                $convertedComponentName
            ],
            glue: DIRECTORY_SEPARATOR
        );

        $componentContent = ConversionManager::make()->path($fullPath)->convert();
        $viewContent = RenderedViewContent::make()->path($fullPath)->content();

        $this->info("Converting $convertedComponentName");
        $this->newLine();

        if (! File::exists($convertedComponentPath)) {
            File::makeDirectory($convertedComponentPath, 0755, true, true);
        }

        File::put($convertedComponentPath.DIRECTORY_SEPARATOR.$convertedComponentName.'.php', $componentContent);
        $this->info('Fomatting File');
        FormatFile::make()->path($convertedComponentPath)->name($convertedComponentName)->format();
        File::put($convertedComponentPath.DIRECTORY_SEPARATOR.$emoji.$convertedComponentName.'.blade.php', $viewContent);

        if(config('livewire-v4-patch.create_js')){
            File::put($convertedComponentPath.DIRECTORY_SEPARATOR.$convertedComponentName.'.js', "");
        }

        if(config('livewire-v4-patch.create_css')){
            File::put($convertedComponentPath.DIRECTORY_SEPARATOR.$convertedComponentName.'.css', "");
        }

        if(config('livewire-v4-patch.global_css')){
            File::put($convertedComponentPath.DIRECTORY_SEPARATOR.$convertedComponentName.'.global.css', "");
        }

        $this->info('DONE !!!');

        $viewFilePath = ViewFilePath::make()->path($fullPath)->viewFilePath();
        File::delete($fullPath);
        File::delete($viewFilePath);

        // check if the base directory is empty
        if (File::isEmptyDirectory(dirname($fullPath))) {
            File::deleteDirectory(dirname($fullPath));
            $this->info('Cleaning the Empty Componet Directoy');
        }
        if (File::isEmptyDirectory(dirname($viewFilePath))) {
            File::deleteDirectory(dirname($viewFilePath));
            $this->info('Cleaning the Empty View Directoy');
        }

        $this->newLine();
    }
}
