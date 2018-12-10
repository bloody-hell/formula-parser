<?php
namespace bloodyHell\formulaParser\operators;


use bloodyHell\formulaParser\FormulaParser;


interface IOperator
{
    public function process(FormulaParser $parser, string $formula): string;
}