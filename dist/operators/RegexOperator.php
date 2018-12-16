<?php
namespace bloodyHell\formulaParser\operators;


use bloodyHell\formulaParser\FormulaParser;
use bloodyHell\formulaParser\operands\DynamicOperand;
use bloodyHell\formulaParser\operands\IFormula;


class RegexOperator implements IOperator
{
    /** @var string */
    private $regex;

    /** @var callable */
    private $callback;

    /**
     * RegexOperator constructor.
     * @param string   $regex
     * @param callable $callback
     */
    public function __construct (string $regex, callable $callback)
    {
        $this->regex    = $regex;
        $this->callback = $callback;
    }

    private function generateProcessingExpression(IFormula $a, IFormula $b): callable
    {
        return function($item)use($a, $b){
            return call_user_func($this->callback, $a->generateValue($item), $b->generateValue($item));
        };
    }


    public function process(FormulaParser $parser, string $formula): string
    {
        $callback = function($matches)use($parser){

            $expression = $this->generateProcessingExpression(
                $parser->parseOperand($matches[1]),
                $parser->parseOperand($matches[2])
            );

            return $parser->tokenize(new DynamicOperand($expression));

        };

        do {
            $formula = preg_replace_callback($this->regex, $callback, $formula, 1, $count);
        } while ($count > 0);

        return $formula;
    }
}