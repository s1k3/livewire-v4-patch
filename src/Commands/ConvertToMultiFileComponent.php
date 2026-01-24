<?php

namespace LivewireV4\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use LivewireV4\Converter\ConversionManager;
use LivewireV4\Utility\RenderedViewContent;

class ConvertToMultiFileComponent extends Command
{
    protected $signature = 'convert-class-to:mfc {path}';

    protected $description = 'Convert Livewire class based component to Multi File Component(MFC)';

    public function handle(): void
    {
        $path = $this->argument('path');
        $classComponentPath = config('livewire-v4-patch.class_component_path');
        $mfcComponentPath = config('livewire-v4-patch.mfc_component_path');

        $fullPath = $classComponentPath.DIRECTORY_SEPARATOR.$path;

        if (File::isFile($fullPath)) {

            $componentDirectory = str()->of(dirname($path))->lower();
            $convertedComponentName = str()->of(File::name($fullPath))->kebab()->toString();
            $convertedComponentPath = $mfcComponentPath.DIRECTORY_SEPARATOR.$componentDirectory.DIRECTORY_SEPARATOR.$convertedComponentName;

            $componentContent = ConversionManager::make()->path($fullPath)->convert();
            $viewContent = RenderedViewContent::make()->path($fullPath)->content();

            $this->info("Converting $convertedComponentName");
            $this->newLine();

            if (! File::exists($convertedComponentPath)) {
                File::makeDirectory($convertedComponentPath, 0755, true, true);
            }

            File::put($convertedComponentPath.DIRECTORY_SEPARATOR.$convertedComponentName.'.php', $componentContent);
            File::put($convertedComponentPath.DIRECTORY_SEPARATOR.$convertedComponentName.'.blade.php', $viewContent);

            $this->info('DONE !!!');
            $this->newLine();
        }
    }
}
