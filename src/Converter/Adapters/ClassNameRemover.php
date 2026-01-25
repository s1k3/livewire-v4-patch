<?php

namespace LivewireV4\Converter\Adapters;

use Closure;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class ClassNameRemover
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
                    $classNode = new Class_(
                        null,
                        [
                            'flags' => $node->flags,
                            'extends' => $node->extends,
                            'implements' => $node->implements,
                            'stmts' => $node->stmts,
                            'attrGroups' => $node->attrGroups,
                        ]
                    );

                    $classNode->setAttribute('comments', $node->getComments());

                    $newExpr = new New_($classNode);

                    return new Expression($newExpr);
                }

                return null;
            }
        });

        $modifiedAst = $traverser->traverse($ast);

        $prettyPrinter = new Standard;

        $newCode = $prettyPrinter->prettyPrintFile($modifiedAst);

        return $next($newCode);
    }
}
