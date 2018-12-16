<?php
namespace bloodyHell\formulaParser\operands;


class DynamicOperand implements IFormula
{
    /** @var callable */
    private $expression;

    /**
     * DynamicOperand constructor.
     * @param callable $expression
     */
    public function __construct (callable $expression)
    {
        $this->expression = $expression;
    }

    public function generateValue ($item)
    {
        return call_user_func($this->expression, $item);
    }
}