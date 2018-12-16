<?php
namespace bloodyHell\formulaParser\operands;


interface IFormula
{
    public function generateValue($item);
}