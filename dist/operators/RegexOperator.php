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

    private function replacementCallback(FormulaParser $parser): callable
    {
        return function($matches)use($parser){

            $a = $parser->parseOperator($matches[1]);
            $b = $parser->parseOperator($matches[2]);

            if(is_float($a)) {
                if(is_float($b)) {

                    return $parser->tokenize(function()use($a, $b){
                        return call_user_func($this->callback, $a, $b);
                    });

                } else {
                    return $parser->tokenize(function($item)use($a, $b){
                        return call_user_func($this->callback, $a, call_user_func($b, $item));
                    });
                }
            } else {
                if(is_float($b)) {
                    return $parser->tokenize(function($item)use($a, $b){
                        return call_user_func($this->callback, call_user_func($a, $item), $b);
                    });
                } else {
                    return $parser->tokenize(function($item)use($a, $b){
                        return call_user_func($this->callback, call_user_func($a, $item), call_user_func($b, $item));
                    });
                }
            }

        };
    }

    public function process(FormulaParser $parser, string $formula): string
    {
        do {
            $formula = preg_replace_callback($this->regex, $this->replacementCallback($parser), $formula, 1, $count);
        } while ($count > 0);

        return $formula;
    }
}