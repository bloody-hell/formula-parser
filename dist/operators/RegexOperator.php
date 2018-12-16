<?php
namespace bloodyHell\formulaParser\operators;


use bloodyHell\formulaParser\FormulaParser;


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

    private function generateProcessingExpression($a, $b): callable
    {
        if(is_float($a)) {
            if(is_float($b)) {

                return function()use($a, $b){
                    return call_user_func($this->callback, $a, $b);
                };

            } else {
                return function($item)use($a, $b){
                    return call_user_func($this->callback, $a, call_user_func($b, $item));
                };
            }
        } else {
            if(is_float($b)) {
                return function($item)use($a, $b){
                    return call_user_func($this->callback, call_user_func($a, $item), $b);
                };
            } else {
                return function($item)use($a, $b){
                    return call_user_func($this->callback, call_user_func($a, $item), call_user_func($b, $item));
                };
            }
        }
    }


    public function process(FormulaParser $parser, string $formula): string
    {
        $callback = function($matches)use($parser){

            return $parser->tokenize($this->generateProcessingExpression(
                $parser->parseOperator($matches[1]),
                $parser->parseOperator($matches[2])
            ));

        };

        do {
            $formula = preg_replace_callback($this->regex, $callback, $formula, 1, $count);
        } while ($count > 0);

        return $formula;
    }
}