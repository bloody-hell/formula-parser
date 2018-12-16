<?php
namespace bloodyHell\formulaParser\operands;


class SqlOperand implements IFormula
{
    /** @var float */
    private $value;

    /**
     * SqlOperand constructor.
     * @param string $value
     */
    public function __construct (string $value)
    {
        $this->value = $value;
    }

    public function generateValue ($item)
    {
        return $this->value;
    }

}