<?php

namespace LivewireV4\Utility;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Exception;
use LivewireV4\Interface\Instance;

class DirecoryListing implements Instance
{

    private $filePath = null;

    public static function make(): Instance
    {
        return new static();
    }

    public function path($filePath): static {
        $this->filePath = $filePath;
        return $this;
    }

    public function fileListings(): array
    {
        if($this->filePath === null){
            $this->filePath = base_path('resources/views');
        }

        dd(File::allFiles($this->filePath));

        return $excludedDirectories;
    }
}