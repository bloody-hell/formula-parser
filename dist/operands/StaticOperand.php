<?php
namespace bloodyHell\formulaParser\operands;


class StaticOperand implements IFormula
{
    /** @var float */
    private $value;

    /**
     * StaticOperand constructor.
     * @param float $value
     */
    public function __construct (float $value)
    {
        $this->value = $value;
    }

    public function generateValue ($item)
    {
        return $this->value;
    }

}