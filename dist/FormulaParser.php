<?php
namespace bloodyHell\formulaParser;



class FormulaParser
{
    /** @var int */
    private $_counter = 0;

    /** @var callable[] */
    private $_values = [];

    /** @var callable[] */
    private $_tokens = [];

    /** @var operators\IOperator[] */
    private $_operators = [];

    /**
     * FormulaParser constructor.
     * @param callable[] $values
     */
    public function __construct (array $values)
    {
        $this->_values = $values;

        $operatorRegex = '([\da-z:.]*)';

        $this->_operators = [
            new operators\RegexOperator('@'.$operatorRegex.'\^'.$operatorRegex.'@i', function($a, $b){return pow($a, $b);}),
            new operators\RegexOperator('@'.$operatorRegex.'\/'.$operatorRegex.'@i', function($a, $b){return $a / $b;}),
            new operators\RegexOperator('@'.$operatorRegex.'\*'.$operatorRegex.'@i', function($a, $b){return $a * $b;}),
            new operators\RegexOperator('@'.$operatorRegex.'\-'.$operatorRegex.'@i', function($a, $b){return $a - $b;}),
            new operators\RegexOperator('@'.$operatorRegex.'\+'.$operatorRegex.'@i', function($a, $b){return $a + $b;}),
        ];
    }

    /**
     * @param string $formula
     * @return callable
     * @throws \bloodyHell\formulaParser\ParseException
     */
    public function parse(string $formula): callable
    {
        $this->_counter = 0;
        $this->_tokens = [];

        while($parenthesis = $this->findParenthesis($formula)) {

            list($start, $end) = $parenthesis;

            $formula = $this->replaceParenthesisWithToken($formula, $start, $end);
        }

        return $this->parseFlat($formula);
    }

    /**
     * @param string $formula
     * @param int    $start
     * @param int    $end
     * @return string
     * @throws \bloodyHell\formulaParser\ParseException
     */
    private function replaceParenthesisWithToken(string $formula, int $start, int $end): string
    {
        $sub = substr($formula, $start, $end - $start + 1);

        $token = $this->tokenize($this->parseFlat(substr($sub, 1, strlen($sub) - 2)));

        return substr($formula, 0, $start) . $token . substr($formula, $end + 1);
    }

    /**
     * @param string $formula
     * @return array|null
     * @throws \bloodyHell\formulaParser\ParseException
     */
    private function findParenthesis(string $formula): ?array
    {
        while(false === $end = strpos($formula, ')')) {
            return null;
        }
        if(false === $start = strrpos(substr($formula,0, $end), '(')) {
            throw new ParseException('No matching parenthesis');
        }
        return [$start, $end];
    }

    public function tokenize(callable $value): string
    {
        $token = ':t'.$this->_counter++;
        $this->_tokens[$token] = $value;
        return $token;
    }

    /**
     * @param string $operator
     * @return callable|float
     * @throws ParseException
     */
    public function parseOperator(string $operator)
    {
        if(!$operator) {
            return 0.0;
        }
        if(isset($this->_tokens[$operator])) {
            return $this->_tokens[$operator];
        }
        if (isset($this->_values[$operator])) {
            return $this->_values[$operator];
        }
        if (is_numeric($operator)) {
            return (float)$operator;
        }
        throw new ParseException('Unknown operator type: ' . $operator);
    }

    /**
     * @param string $formula
     * @return callable
     * @throws \bloodyHell\formulaParser\ParseException
     */
    private function parseFlat(string $formula): callable
    {
        foreach ($this->_operators as $operator) {
            $formula = $operator->process($this, $formula);
        }

        if(!isset($this->_tokens[$formula])) {
            throw new ParseException('Flat parse error: ' . $formula);
        }

        return $this->_tokens[$formula];
    }
}