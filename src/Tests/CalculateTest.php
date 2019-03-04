<?php

namespace stuton\CalculateExpressionBundle\Tests;

use DivisionByZeroError;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use stuton\CalculateExpressionBundle\Entity\CalculateExpression;

class CalculateTest extends TestCase
{
    public function getCorrectExpressions()
    {
        return [
            ["2 + 3", 5],
            ["0/2", 0],
            ["((5+3)*2)+(6-5.2)*3", 18.4],
            ["(10/2)+(19.9999+10.0001)*2 - 65", 0],
        ];
    }

    public function getErrorExpressions()
    {
        return [
            ["10/0", DivisionByZeroError::class, "Division by zero"],
            ["(((5+3)*2)+(6-5).2)*3", InvalidArgumentException::class, "The number of operators does not match the number of operands"],
            ["=!!(19.9999)", InvalidArgumentException::class, "Not enough data to count!"],
            ["sdasdasdada", InvalidArgumentException::class, "Not enough data to count!"],
            ["0", InvalidArgumentException::class, "Not enough data to count!"],
            ["", InvalidArgumentException::class, "Not enough data to count!"],
        ];
    }

    /**
     * @dataProvider getCorrectExpressions
     * @param $expression
     * @param $expectedValue
     * @throws \Exception
     */
    public function testCalculate($expression, $expectedValue)
    {
        $calculate = new CalculateExpression();
        $this->assertEquals($expectedValue, $calculate->calculate($expression));
    }

    /**
     * @dataProvider getErrorExpressions
     * @param $expression
     * @param $exceptionClass
     * @param $exceptionMessage
     * @throws \Exception
     */
    public function testExceptionCalculate($expression, $exceptionClass, $exceptionMessage)
    {
        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);
        $calculate = new CalculateExpression();
        $calculate->calculate($expression);
    }
}