<?php

namespace LivewireV4\Converter\Adapters;

use Closure;

class InsertMount
{
    public function __invoke(string $content, Closure $next) : string
    {
    
        if(str($content)->contains('mount')){
            return $next($content);
        }

        $mountFunction = <<<'TEXT'
            public function mount()
            {
                
            }

            public function render()

        TEXT;

        $nextPassable = str()->of($content)
            ->replace("public function render()", $mountFunction)
            ->toString();

        return $next($nextPassable);
    }
}