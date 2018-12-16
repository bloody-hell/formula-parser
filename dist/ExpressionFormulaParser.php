<?php
namespace bloodyHell\formulaParser;


class ExpressionFormulaParser
{
    /** @var \bloodyHell\formulaParser\BaseFormulaParser */
    private $_parser;

    private function defaultOperators(): array
    {
        $operatorRegex = '([\da-z:.]*)';

        return [
            '^' => new operators\RegexOperator('@'.$operatorRegex.'\^'.$operatorRegex.'@i', function($a, $b){return pow($a, $b);}),
            '/' => new operators\RegexOperator('@'.$operatorRegex.'\/'.$operatorRegex.'@i', function($a, $b){return $a / $b;}),
            '*' => new operators\RegexOperator('@'.$operatorRegex.'\*'.$operatorRegex.'@i', function($a, $b){return $a * $b;}),
            '-' => new operators\RegexOperator('@'.$operatorRegex.'\-'.$operatorRegex.'@i', function($a, $b){return $a - $b;}),
            '+' => new operators\RegexOperator('@'.$operatorRegex.'\+'.$operatorRegex.'@i', function($a, $b){return $a + $b;}),
        ];
    }

    /**
     * ExpressionFormulaParser constructor.
     * @param callable[]            $values
     * @param operators\IOperator[] $operators
     */
    public function __construct (array $values, array $operators)
    {
        $values = array_map(function(callable $value){
            return new operands\DynamicOperand($value);
        }, $values);

        $operators = array_merge($this->defaultOperators(), $operators);

        $this->_parser = new BaseFormulaParser($values, $operators);
    }

    /**
     * @param string $formula
     * @return callable
     * @throws \bloodyHell\formulaParser\ParseException
     */
    public function parse(string $formula): callable
    {
        return function($item)use($formula){
            return $this->_parser->parse($formula)->generateValue($item);
        };
    }
}