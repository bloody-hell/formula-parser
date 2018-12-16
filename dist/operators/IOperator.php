<?php
namespace bloodyHell\formulaParser\operators;


use bloodyHell\formulaParser\BaseFormulaParser;


interface IOperator
{
    public function process(BaseFormulaParser $parser, string $formula): string;
}