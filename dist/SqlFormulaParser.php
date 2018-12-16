<?php
namespace bloodyHell\formulaParser;


class SqlFormulaParser
{
    /** @var \bloodyHell\formulaParser\BaseFormulaParser */
    private $_parser;

    private function defaultOperators(): array
    {
        $operatorRegex = '([\da-z:.]*)';

        return [
            '^' => new operators\RegexOperator('@'.$operatorRegex.'\^'.$operatorRegex.'@i', function($a, $b){return 'pow('.$a.', '.$b.')';}),
            '/' => new operators\RegexOperator('@'.$operatorRegex.'\/'.$operatorRegex.'@i', function($a, $b){return '('.$a . '/' . $b . ')';}),
            '*' => new operators\RegexOperator('@'.$operatorRegex.'\*'.$operatorRegex.'@i', function($a, $b){return '('.$a . '*' . $b . ')';}),
            '-' => new operators\RegexOperator('@'.$operatorRegex.'\-'.$operatorRegex.'@i', function($a, $b){return '('.$a . '-' . $b . ')';}),
            '+' => new operators\RegexOperator('@'.$operatorRegex.'\+'.$operatorRegex.'@i', function($a, $b){return '('.$a . '+' . $b . ')';}),
        ];
    }

    /**
     * ExpressionFormulaParser constructor.
     * @param string[] $values
     * @param operators\IOperator[] $operators
     */
    public function __construct (array $values, array $operators = [])
    {
        $values = array_map(function(string $value){
            return new operands\SqlOperand($value);
        }, $values);

        $operators = array_merge($this->defaultOperators(), $operators);

        $this->_parser = new BaseFormulaParser($values, $operators);
    }

    /**
     * @param string $formula
     * @return string
     * @throws \bloodyHell\formulaParser\ParseException
     */
    public function parse(string $formula): string
    {
        return $this->_parser->parse($formula)->generateValue(null);
    }
}