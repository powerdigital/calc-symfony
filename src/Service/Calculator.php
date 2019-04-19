<?php

namespace App\Service;

use Exception;
use InvalidArgumentException;
use SplStack;
use Throwable;

class Calculator
{
    /**
     * @var string Valid arithmetic expression pattern
     */
    private const EXPR_PATTERN = '/^[0-9a-zA-z\s\(]*[0-9a-zA-z\s\+\-\*\/\^\(\)\.]*[0-9a-zA-Z\)]+$/';

    /**
     * @var int Valid expression length
     */
    private const EXPR_MAX_LENGTH = 50;

    /**
     * @var array Arithmetic operators list
     */
    private const OPERATORS = ['+', '-', '*', '/', '^'];

    /**
     * @var string Opening parenthesis
     */
    private const OPEN_BLOCK = '(';

    /**
     * @var string Opening parenthesis
     */
    private const CLOSE_BLOCK = ')';

    /**
     * @var array Operators priorities
     */
    private const PRIORITIES = [
        '(' => 0,
        ')' => 0,
        '+' => 1,
        '-' => 1,
        '*' => 2,
        '/' => 2,
        '^' => 3,
    ];

    /**
     * @var array Mathematical constants
     */
    private const MATH_CONSTANTS = [
        'Pi' => M_PI,
        'E' => M_E,
    ];

    /**
     * @var string Invalid expression error message
     */
    public const ERROR = 'ERROR';

    /**
     * @var string Arithmetic expression
     */
    private $expr;

    /**
     * @var array User defined variables from previous inputs
     */
    private $memory = [];

    public function __construct(string $expr = null)
    {
        $this->expr = $expr;
    }

    /**
     * Set expression field
     *
     * @param string $expr Arithmetic expression string
     */
    public function setExpr(string $expr)
    {
        $this->expr = $expr;
    }

    /**
     * Parse and evaluate arithmetic expression
     *
     * @return string The result of calculation or error message
     * @throws Exception
     */
    public function calculate(): string
    {
        try {
            // Get provided expression and clear from spaces
            $expr = $this->expr;
            $expr = trim(str_replace(' ', '', $expr));

            if (!self::isValid($expr)) {
                return self::ERROR;
            }

            // Initiate stack objects for digits and operators
            $numStack = new SplStack();
            $operStack = new SplStack();

            $length = strlen($expr);

            // Iterate each symbol in the expression
            for ($i = 0; $i < $length; $i++) {
                $prevOperator = !$operStack->isEmpty() ? $operStack->top() : null;
                $currElement = $expr[$i];

                if (self::isDigit($currElement)) {
                    $result = '';

                    while (isset($expr[$i]) && self::isDigit($expr[$i])) {
                        $result .= $expr[$i];
                        $i++;
                    }

                    // We have to reset index after last iteration
                    // because it served checking purposes and may break the algorithm logic
                    $i--;

                    $numStack->push((float) $result);
                } elseif (self::isOperator($currElement)) {
                    if (!isset($prevOperator)) {
                        $operStack->push($currElement);

                        continue;
                    }

                    if (self::PRIORITIES[$currElement] > self::PRIORITIES[$prevOperator]) {
                        if (!self::isOpening(self::PRIORITIES[$prevOperator])) {
                            $operStack->push($currElement);

                            continue;
                        }
                    }

                    $currNum = $numStack->pop();
                    $prevNum = $numStack->pop();

                    $resultNum = self::evaluate($prevNum, $currNum, $prevOperator);

                    $numStack->push((float) $resultNum);
                    $operStack->pop();

                    // We have to reset index after last operation
                    $i--;
                } elseif (self::isChar($currElement)) {
                    $result = '';

                    while (isset($expr[$i]) && self::isChar($expr[$i])) {
                        $result .= $expr[$i];
                        $i++;
                    }

                    // We have to reset index after last iteration
                    // because it was for checking purposes and may break the algorithm logic
                    $i--;

                    $converted = self::MATH_CONSTANTS[$result] ?? $this->memory[$result] ?? null;

                    if (empty($converted)) {
                        return self::ERROR;
                    }

                    $numStack->push((float) $converted);
                } elseif (self::isOpening($currElement)) {
                    // Add opening parenthesis in stack without additional conditions
                    $operStack->push($currElement);
                } elseif (self::isClosing($currElement)) {
                    // If previous operator is opening parenthesis, remove them and move forward
                    if (self::isOpening($prevOperator)) {
                        $operStack->pop();

                        continue;
                    }

                    $i--;

                    // Calculate local expression
                    $currNum = $numStack->pop();
                    $prevNum = $numStack->pop();

                    $resultNum = self::evaluate($prevNum, $currNum, $prevOperator);

                    $numStack->push($resultNum);
                    $operStack->pop();
                } else {
                    return self::ERROR;
                }
            }

            while ($numStack->count() !== 1) {
                $currNum = $numStack->pop();
                $prevNum = $numStack->pop();
                $prevOperator = $operStack->pop();

                $resultNum = self::evaluate($prevNum, $currNum, $prevOperator);
                $numStack->push($resultNum);
            }

            return (string) round($numStack->pop(), 5);
        } catch (Throwable $e) {
            return self::ERROR;
        }
    }

    /**
     * @param int $firstOperand The first operand of expression
     * @param int $secondOperand The second operand of expression
     * @param string $operator Arithmetic operator
     *
     * @return int Evaluated result
     * @throws InvalidArgumentException
     */
    public static function evaluate(float $firstOperand, float $secondOperand, string $operator): float
    {
        if (!in_array($operator, self::OPERATORS)) {
            throw new InvalidArgumentException('Invalid operator detected: ' . $operator);
        }

        switch ($operator) {
            case '+':
                $result = $firstOperand + $secondOperand;
                break;
            case '-':
                $result = $firstOperand - $secondOperand;
                break;
            case '*':
                $result = $firstOperand * $secondOperand;
                break;
            case '/':
                $result = $firstOperand / $secondOperand;
                break;
            case '^':
                $result = $firstOperand ** $secondOperand;
                break;
        }

        return $result;
    }

    /**
     * Validate arithmetic  expression
     *
     * @param string $expr Expression string
     *
     * @return bool True if expression if valid
     */
    public static function isValid(string $expr): bool
    {
        return preg_match(self::EXPR_PATTERN, $expr) && strlen($expr) <= self::EXPR_MAX_LENGTH;
    }

    public static function isOperator(string $str): bool
    {
        return in_array($str, self::OPERATORS);
    }

    public static function isOpening(string $str): bool
    {
        return self::OPEN_BLOCK === $str;
    }

    public static function isClosing(string $str): bool
    {
        return self::CLOSE_BLOCK === $str;
    }

    public static function isDigit(string $str): bool
    {
        return is_numeric($str) || strpos($str, '.') !== false;
    }

    public static function isChar(string $str): bool
    {
        $matches = null;
        preg_match('/^[a-zA-Z]+$/', $str, $matches);

        return !empty($matches);
    }
}
