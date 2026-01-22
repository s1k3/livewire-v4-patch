<?php

namespace LivewireV4\Converter\Adapters;

use Closure;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\NodeFinder;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter;
use PhpParser\PrettyPrinter\Standard;

class RemoveRender
{
    public function __invoke(string $content, Closure $next) : string
    {
        $parser = (new ParserFactory())->createForHostVersion();
        $ast = $parser->parse($content);

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new class extends NodeVisitorAbstract {
            

            public function enterNode(Node $node) {
                if ($node instanceof ClassMethod && $node->name->name === 'render') {
                    return false;
                }
                return true;
            }
        });

        $modifiedAst = $traverser->traverse($ast);

        // Output the modified code
        $prettyPrinter = new PrettyPrinter\Standard();
        $newCode = $prettyPrinter->prettyPrintFile($modifiedAst);

        return $next($newCode);
    }
}