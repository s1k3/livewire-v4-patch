<?php

namespace LivewireV4\Converter\Adapters;

use Closure;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter;

class MoveCodeToMount
{
    public function __invoke(string $content, Closure $next) : string
    {
        // Parse the code - FIXED HERE
        $parser = (new ParserFactory)->createForHostVersion();
        $ast = $parser->parse($content);

        // Create a visitor to modify the mount method
        $traverser = new NodeTraverser();
        dd($traverser);
        $traverser->addVisitor(new class extends NodeVisitorAbstract {
            public function enterNode(Node $node) {
                if ($node instanceof ClassMethod && $node->name->name === 'mount') {
                    // Parse the new code into AST nodes
                    $parser = (new ParserFactory)->createForHostVersion(); // FIXED HERE
                    
                    $newCode = <<<'CODE'
                    // Injected via LivewireV4 Converter
                    $this->userId = auth()->id();
                    \Log::info("Mount called for user: " . auth()->id());
                    CODE;
                    
                    try {
                        $newStatements = $parser->parse('<?php ' . $newCode);
                        
                        // Insert at the beginning of the method body
                        if ($newStatements) {
                            array_unshift($node->stmts, ...$newStatements);
                        }
                    } catch (\Exception $e) {
                        // Handle parse errors gracefully
                    }
                }
                return $node;
            }
        });

        // Apply the transformations
        $modifiedAst = $traverser->traverse($ast);

        // Output the modified code
        $prettyPrinter = new PrettyPrinter\Standard();
        $newCode = $prettyPrinter->prettyPrintFile($modifiedAst);

        return $next($newCode);
    }
}