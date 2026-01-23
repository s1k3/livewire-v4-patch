<?php

namespace LivewireV4\Converter\Adapters;

use Closure;
use LivewireV4\Utility\NamespaceGuesser;
use ReflectionClass;

class ClassNameRemover
{
    public function __invoke(string $content, Closure $next): string
    {
        $className = str()->of($content)->after('class')->before('extends')->trim()->toString();
        $nextPassable = str()->of($content)->after(';')->replace($className, '')->replace('class', 'new class')->replaceEnd("}\n", '};')->toString();
        return $next($nextPassable);
    }
}
