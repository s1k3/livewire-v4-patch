<?php

namespace LivewireV4\Converter;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Pipeline;
use LivewireV4\Converter\Adapters\ClassNameRemover;
use LivewireV4\Converter\Adapters\InsertMount;
use LivewireV4\Converter\Adapters\LazyAttributeModify;
use LivewireV4\Converter\Adapters\MoveCodeToMount;
use LivewireV4\Converter\Adapters\NamespaceRemover;
use LivewireV4\Converter\Adapters\RemoveRender;
use LivewireV4\Interface\Instance;

class ConversionManager implements Instance
{
    private $filePath = null;

    public static function make(): static
    {
        return new static;
    }

    public function path($filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function convert(): string
    {

        return Pipeline::send(
            passable: File::get($this->filePath)
        )
            ->through([
                ClassNameRemover::class,
                LazyAttributeModify::class,
                NamespaceRemover::class,
                InsertMount::class,
                MoveCodeToMount::class,
                RemoveRender::class,
            ])
            ->thenReturn();

    }
}
