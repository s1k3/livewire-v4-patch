<?php

namespace LivewireV4\Converter\Adapters;

use Closure;

class ModifyRender
{
    public function __invoke(string $content, Closure $next) : string
    {
       $renderContentBeforeView = str()->of($content)
            ->after("render()")
            ->after("{")
            ->before("return")
            ->toString();

        return $next($content);
    }
}