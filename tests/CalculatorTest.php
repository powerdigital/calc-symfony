<?php

namespace Tests;

use App\Service\Calculator;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private const VALID_EXPRESSION_STRING = ' 10 + 2^5 + (33/3 + (100-50)*2) +6/2 - Pi';
    private const VALID_EXPRESSION_DIGITS = 10 + 2**5 + (33/3 + (100-50)*2) + 6/2 - M_PI;
    private const VALID_EXPRESSION_CONST = '10-E+5';

    private const INVALID_EXPRESSION_EXTRAS = '5 > 2 && (560 + 22)';
    private const INVALID_EXPRESSION_DIGITS = ') 5 > 2';

    /**
     * @throws \Exception
     */
    public function testCalculator()
    {
        $calc = new Calculator(new Logger('calc'), self::VALID_EXPRESSION_STRING);
        $result = $calc->calculate();
        $expected = round((float) self::VALID_EXPRESSION_DIGITS, 5);

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws \Exception
     */
    public function testMathConstants()
    {
        $calc = new Calculator(new Logger('calc'), self::VALID_EXPRESSION_CONST);
        $result = $calc->calculate();

        $this->assertNotNull($result);
    }

    /**
     * @throws \Exception
     */
    public function testUseCases()
    {
        $calc = new Calculator(new Logger('calc'));

        $calc->setExpr('5*(5+2)');
        $result = $calc->calculate();
        $this->assertEquals(35, $result);

        $calc->setExpr('5');
        $result = $calc->calculate();
        $this->assertEquals(5, $result);

        $calc->setExpr('(5/5*2)');
        $result = $calc->calculate();
        $this->assertEquals(2, $result);

        $calc->setExpr('100/2 + 20*3 + (2 - 1)');
        $result = $calc->calculate();
        $this->assertEquals(111, $result);

        $calc->setExpr('Pi');
        $result = $calc->calculate();
        $this->assertEquals(3.14159, $result);

        $calc->setExpr('E');
        $result = $calc->calculate();
        $this->assertEquals(2.71828, $result);

        $calc->setExpr('Pi/E');
        $result = $calc->calculate();
        $this->assertEquals(1.15573, $result);

        $calc->setExpr('222/2 + 2*Pi');
        $result = $calc->calculate();
        $this->assertEquals(117.28319, $result);

        $calc->setExpr('222/2 + 111*2');
        $result = $calc->calculate();
        $this->assertEquals(333, $result);

        $calc->setExpr('1000000000*1000000000');
        $result = $calc->calculate();
        $this->assertEquals('1.0E+18', $result);

        $calc->setExpr('1000000000/1000000000');
        $result = $calc->calculate();
        $this->assertEquals(1, $result);

        $calc->setExpr('0.2+0.5');
        $result = $calc->calculate();
        $this->assertEquals(0.7, $result);

        $calc->setExpr('0.5*0.5');
        $result = $calc->calculate();
        $this->assertEquals(0.25, $result);
    }

    public function testValidation()
    {
        $this->assertTrue(Calculator::isValid(self::VALID_EXPRESSION_STRING));
        $this->assertFalse(Calculator::isValid(self::INVALID_EXPRESSION_EXTRAS));
        $this->assertFalse(Calculator::isValid(self::INVALID_EXPRESSION_DIGITS));
    }

    /**
     * @throws \Exception
     */
    public function testEvaluation()
    {
        $this->assertEquals(10, Calculator::evaluate(5, 5, '+'));
        $this->assertEquals(8, Calculator::evaluate(18, 10, '-'));
        $this->assertEquals(12, Calculator::evaluate(3, 4, '*'));
        $this->assertEquals(5, Calculator::evaluate(25, 5, '/'));
        $this->assertEquals(8, Calculator::evaluate(2, 3, '^'));
    }

    public function testCheckers()
    {
        $this->assertTrue(Calculator::isOpening('('));
        $this->assertTrue(Calculator::isClosing(')'));

        $this->assertTrue(Calculator::isOperator('+'));
        $this->assertTrue(Calculator::isOperator('-'));
        $this->assertTrue(Calculator::isOperator('*'));
        $this->assertTrue(Calculator::isOperator('/'));
        $this->assertTrue(Calculator::isOperator('^'));

        $this->assertTrue(Calculator::isDigit('15'));
        $this->assertTrue(Calculator::isDigit(35));

        $this->assertTrue(Calculator::isChar('Pi'));
        $this->assertTrue(Calculator::isChar('E'));
        $this->assertTrue(Calculator::isChar('myvar'));
    }
}
