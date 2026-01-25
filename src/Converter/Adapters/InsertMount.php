<?php

namespace LivewireV4\Converter\Adapters;

use Closure;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class InsertMount
{
    public function __invoke(string $content, Closure $next): string
    {


        $parser = (new ParserFactory)->createForHostVersion();

        $ast = $parser->parse($content);

        $nodeFinder = new NodeFinder;

        $existingMount = $nodeFinder->findFirst($ast, function (Node $node) {
            return $node instanceof ClassMethod && $node->name->name === 'mount';
        });

        if ($existingMount) {
            return $next($content);
        }

        $builder = new BuilderFactory;

        $mountMethod = $builder->method('mount')
            ->makePublic()
            ->addStmts([])
            ->getNode();

        $traverser = new NodeTraverser;
        $traverser->addVisitor(new class($mountMethod) extends \PhpParser\NodeVisitorAbstract
        {
            private $mountMethod;

            public function __construct($mountMethod)
            {
                $this->mountMethod = $mountMethod;
            }

            public function enterNode(Node $node)
            {
                if ($node instanceof Class_) {

                    $properties = [];
                    $otherStmts = [];

                    foreach ($node->stmts as $stmt) {

                        match (true) {
                            $stmt instanceof Property => $properties[] = $stmt,
                            default => $otherStmts[] = $stmt
                        };

                    }

                    $node->stmts = [
                        ...$properties,
                        $this->mountMethod,
                        ...$otherStmts,
                    ];
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
