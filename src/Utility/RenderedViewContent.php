<?php

namespace LivewireV4\Utility;

use Exception;
use Illuminate\Support\Facades\File;
use LivewireV4\Interface\Instance;

class RenderedViewContent implements Instance
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

    public function content(): string
    {
        if ($this->filePath === null) {
            throw new Exception('File path is not set.');
        }

        return ViewFilePath::make()->path($this->filePath)->viewFilePath();
    }
}
