<?php

namespace LivewireV4\Converter\Adapters;

use Closure;

class LazyAttributeModify
{
    public function __invoke(string $content, Closure $next): string
    {
        if (preg_match('/#\[\s*Lazy\s*(?:\(\s*([^)]*?)\s*\))?\s*\]/', $content, $matches)) {
            $passableContent = str()->of($content)->replace($matches[0], '')->replace('new', "new {$matches[0]}")->toString();

            return $next($passableContent);
        }

        return $next($content);
    }
}
