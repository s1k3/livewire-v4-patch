<?php

namespace LivewireV4\Converter\Adapters;

use Closure;

class NamespaceRemover
{
    public function __invoke(string $content, Closure $next) 
    {
        $className = str()->of($content)->after('class')->before('extends')->trim()->toString();
        $nextPassable = str()->of($content)->replace($className, "")->replaceEnd("}\n", "};")->toString();
        return $next($nextPassable);
    }
}
