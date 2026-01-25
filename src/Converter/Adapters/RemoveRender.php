<?php

namespace LivewireV4\Converter\Adapters;

use Closure;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class RemoveRender
{
    public function __invoke(string $content, Closure $next): string
    {
        $parser = (new ParserFactory)->createForHostVersion();

        $ast = $parser->parse($content);

        $traverser = new NodeTraverser;

        $traverser->addVisitor(new class extends NodeVisitorAbstract
        {
            public function leaveNode(Node $node)
            {
                if ($node instanceof Class_) {

                    $node->stmts = array_filter($node->stmts, function ($stmt) {
                        if ($stmt instanceof ClassMethod && $stmt->name->name === 'render') {
                            return false;
                        }

                        return true;
                    });

                    $node->stmts = array_values($node->stmts);
                }

                return $node;
            }
        });

        $modifiedAst = $traverser->traverse($ast);

        $prettyPrinter = new Standard;
        
        $newCode = $prettyPrinter->prettyPrintFile($modifiedAst);

        return $next($newCode);
    }
}
