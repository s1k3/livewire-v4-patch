<?php

namespace LivewireV4\Utility;

use LivewireV4\Interface\Instance;
use RuntimeException;

class FileOperations implements Instance
{
    private $filePath = '';

    private $fileName = '';

    private $fileContents = '';

    public static function make(): static
    {
        return new self;
    }

    public function path(string $path): static
    {
        $this->filePath = $path;

        return $this;
    }

    public function name(string $name): static
    {
        $this->fileName = $name;

        return $this;
    }

    public function contents(string $contents): static
    {
        $this->fileContents = $contents;

        return $this;
    }

    public function save(): bool
    {
        if (! $this->filePath) {
            throw new RuntimeException('File path is not set.');
        }

        if (! $this->fileName) {
            throw new RuntimeException('File name is not set.');
        }

        if (! $this->fileContents) {
            throw new RuntimeException('File contents are not set.');
        }

        if (! is_dir($this->filePath)) {
            mkdir($this->filePath, 0755, true);
        }

        return file_put_contents($this->filePath.DIRECTORY_SEPARATOR.$this->fileName, $this->fileContents) !== false;
    }
}
