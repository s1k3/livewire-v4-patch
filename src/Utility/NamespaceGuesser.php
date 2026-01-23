<?php

namespace LivewireV4\Utility;

use Illuminate\Support\Facades\File;
use LivewireV4\Interface\Instance;
use RuntimeException;
use Symfony\Component\Finder\Finder;

class NamespaceGuesser implements Instance
{
    private $searchDirectories = [];

    private $fileName = null;

    private function __construct()
    {
        $this->searchDirectories = [
            app_path(),
        ];
    }

    public static function make(): static
    {
        return new static;
    }

    public function directories(array $directories): static
    {
        $this->searchDirectories = $directories;

        return $this;
    }

    public function name(string $name): static
    {
        $this->fileName = match (true) {
            str()->of($name)->endswith('.php') => $name,
            default => "{$name}.php"
        };

        return $this;
    }

    public function guess(): string
    {

        if ($this->fileName === null) {
            throw new RuntimeException('File name is not set');
        }

        $finder = new Finder;
        $files = $finder->files()
            ->in($this->searchDirectories)
            ->name($this->fileName);

        if ($files->hasResults()) {
            $iterator = $files->getIterator();
            $iterator->rewind();
            $file = $iterator->current();
            $content = File::get($file->getRealPath());

            return str()->of($content)->after('namespace')->before(';')->trim()->toString();
        }

        return false;
    }
}
