<?php

namespace LivewireV4\Utility;

use Illuminate\Support\Facades\File;
use LivewireV4\Interface\Instance;

class DirecoryListing implements Instance
{
    private $filePath = null;

    public static function make(): Instance
    {
        return new static;
    }

    public function path($filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function fileListings(): array
    {
        if ($this->filePath === null) {
            $this->filePath = config('laravel-v4-patch.component_path');
        }

        $files = File::allFiles($this->filePath);

        $output = [];
        foreach ($files as $file) {
            if (str()->of($file->getPath())->contains(config(['laravel-v4-patch.excluded_directories']))) {
                continue;
            }

            $output[] = $file->getRealPath();

        }

        return $output;
    }
}
