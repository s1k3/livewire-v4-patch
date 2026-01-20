<?php

namespace LivewireV4\Converter;

use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Facades\File;
use LivewireV4\Converter\Adapters\ClassNameRemover;
use LivewireV4\Converter\Adapters\NamespaceRemover;
use LivewireV4\Interface\Instance;

class ConversionManager implements Instance
{
    private $filePath = null;

    public static function make(): static {
        return new static;
    }

    public function path($filePath){
        $this->filePath = $filePath;
        return $this;
    }

    public function convert(){

        $content = Pipeline::send(
            passable: File::get($this->filePath)
        )
        ->through([
            ClassNameRemover::class,
            NamespaceRemover::class
        ])
        ->thenReturn();

        $componentName = str()->of(File::name($this->filePath))->kebab()->toString();

        dd($content, $componentName);

    }


}