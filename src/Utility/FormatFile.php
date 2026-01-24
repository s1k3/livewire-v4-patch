<?php

namespace LivewireV4\Utility;

use Illuminate\Support\Facades\Process;
use LivewireV4\Interface\Instance as InterfaceInstance;
use RuntimeException;

use function Livewire\str;

class FormatFile implements InterfaceInstance
{
    private $filePath = null;

    private $fileName = null;

    public static function make(): static
    {
        return new self;
    }

    public function path(string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function name(string $fileName): static
    {
        $this->fileName = match(true){
            str()->of($fileName)->endsWith(".php") => $fileName,
            default => "{$fileName}.php"
        };

        return $this;
    }

    public function format(): void
    {

        if ($this->filePath === null) {
            throw new RuntimeException('File Path is not set');
        }

        if ($this->fileName === null) {
            throw new RuntimeException('File Name is not set');
        }

        $fullPath = $this->filePath.DIRECTORY_SEPARATOR.$this->fileName;

        Process::path(base_path())->run("./vendor/bin/pint $fullPath");
    }
}
