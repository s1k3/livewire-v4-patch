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

use function Livewire\str;

class MoveCodeToMount
{


    public function __invoke(string $content, Closure $next) : string
    {
        $parser = (new ParserFactory())->createForHostVersion();
        $ast = $parser->parse("<?php " . str()->of($content)->replace("\n", "")->trim()->toString());

        $traverser = new NodeTraverser();

        $nodeFinder = new NodeFinder();

        $prettyPrinter = new Standard();

        $injectedStatements = [];

        $renderMethod = $nodeFinder->findFirst($ast, function($node) {
            if ($node instanceof ClassMethod && $node->name->name === 'render') {
                return true;
            }
            return false;
        });

        if ($renderMethod && $renderMethod->stmts) {
            foreach ($renderMethod->stmts as $stmt) {
                if ($stmt instanceof Return_) {
                    continue; // Skip return statement
                }
                $injectedStatements[] = $stmt;
            }
        }



        $traverser->addVisitor(new class($injectedStatements) extends NodeVisitorAbstract {
            
            private array $statementsToInject;
            
            public function __construct(array $statementsToInject)
            {
                $this->statementsToInject = $statementsToInject;
            }

            public function enterNode(Node $node) {
                if ($node instanceof ClassMethod && $node->name->name === 'mount') {
                    array_push($node->stmts, ...$this->statementsToInject);
                }
                return $node;
            }
        });

        $modifiedAst = $traverser->traverse($ast);

        // Output the modified code
        $prettyPrinter = new PrettyPrinter\Standard();
        $newCode = $prettyPrinter->prettyPrintFile($modifiedAst);

        return $next($newCode);
    }
}

