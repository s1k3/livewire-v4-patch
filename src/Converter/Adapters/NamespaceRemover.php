<?php

namespace LivewireV4\Converter\Adapters;

use Closure;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Node;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class NamespaceRemover
{
    public function __invoke(string $content, Closure $next): string
    {
        $parser = (new ParserFactory())->createForHostVersion();

        $ast = $parser->parse($content);
        
        $traverser = new NodeTraverser();
        
        $traverser->addVisitor(new class extends NodeVisitorAbstract
        {
            public function leaveNode(Node $node) {
                if ($node instanceof Namespace_) {
                    return $node->stmts;
                }
                
                return null;
            }
        });
        
        $modifiedAst = $traverser->traverse($ast);
        
        $prettyPrinter = new Standard();

        $newCode = $prettyPrinter->prettyPrintFile($modifiedAst);
        
        return $next($newCode);
    }
}