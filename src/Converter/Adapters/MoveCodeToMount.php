<?php

namespace LivewireV4\Converter\Adapters;

use Closure;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

use function Livewire\str;

class MoveCodeToMount
{
    public function __invoke(string $content, Closure $next): string
    {

        $parser = (new ParserFactory)->createForHostVersion();

        $ast = $parser->parse($content);

        $traverser = new NodeTraverser;

        $nodeFinder = new NodeFinder;

        $prettyPrinter = new Standard;

        $injectedStatements = [];

        $renderMethod = $nodeFinder->findFirst($ast, function ($node) {
            if ($node instanceof ClassMethod && $node->name->name === 'render') {
                return true;
            }

            return false;
        });

        if ($renderMethod && $renderMethod->stmts) {
            foreach ($renderMethod->stmts as $stmt) {
                if ($stmt instanceof Return_) {
                    continue;
                }
                $injectedStatements[] = $stmt;
            }
        }

        $traverser->addVisitor(new class($injectedStatements) extends NodeVisitorAbstract
        {
            private array $statementsToInject;

            public function __construct(array $statementsToInject)
            {
                $this->statementsToInject = $statementsToInject;
            }

            public function enterNode(Node $node)
            {
                if ($node instanceof ClassMethod && $node->name->name === 'mount') {
                    array_push($node->stmts, ...$this->statementsToInject);
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
