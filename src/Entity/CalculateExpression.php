<?php

namespace stuton\CalculateExpressionBundle\Entity;

use Psr\Log\InvalidArgumentException;

class CalculateExpression implements CalculateExpressionInterface
{
    /**
     * @param string $expression
     * @return mixed
     * @throws \Exception
     */
    public function calculate(string $expression)
    {
        $expression = $this->clear($expression);
        $polishNotationExpression = $this->convertFromInfixToPolishNotation($expression);
        return $this->calc($polishNotationExpression);
    }

    /**
     * @param string $expression
     * @return mixed|string
     */
    protected function clear(string $expression)
    {
        $expression = str_replace(" ", "", $expression);
        $expression = str_replace(",", ".", $expression);
        return $expression;
    }

    /**
     * @param string $string
     * @return string
     */
    protected function convertFromInfixToPolishNotation(string $string)
    {
        $stack = [];
        $out = [];

        $prior = [
            "^" => ["prior" => "4", "assoc" => "right"],
            "*" => ["prior" => "3", "assoc" => "left"],
            "/" => ["prior" => "3", "assoc" => "left"],
            "+" => ["prior" => "2", "assoc" => "left"],
            "-" => ["prior" => "2", "assoc" => "left"],
            "(" => ["prior" => "0", "assoc" => "none"],
            ")" => ["prior" => "0", "assoc" => "none"],
        ];

        $token = str_split($string);

        if (preg_match("/[\+\-\*\/\^]/", $token['0'])) {
            array_unshift($token, "0");
        }

        $lastNum = true;
        foreach ($token as $key => $value) {

            if (preg_match("/[\+\-\*\/\^]/", $value))
            {
                $endop = false;

                while ($endop != true) {
                    $lastop = array_pop($stack);
                    if ($lastop == "") {
                        $stack[] = $value;
                        $endop = true;
                    } else {
                        $curr_prior = $prior[$value]['prior'];
                        $curr_assoc = $prior[$value]['assoc'];
                        $prev_prior = $prior[$lastop]['prior'];

                        switch ($curr_assoc)
                        {
                            case "left":

                                switch ($curr_prior)
                                {
                                    case ($curr_prior > $prev_prior):
                                        $stack[] = $lastop;
                                        $stack[] = $value;
                                        $endop = true;
                                        break;

                                    case ($curr_prior <= $prev_prior):
                                        $out[] = $lastop;
                                        break;
                                }

                                break;

                            case "right":

                                switch ($curr_prior)
                                {
                                    case ($curr_prior >= $prev_prior):
                                        $stack[] = $lastop;
                                        $stack[] = $value;
                                        $endop = true;
                                        break;

                                    case ($curr_prior < $prev_prior):
                                        $out[] = $lastop;
                                        break;
                                }

                                break;

                        }

                    }
                }
                $lastNum = false;
            } elseif (preg_match("/[0-9\.]/", $value)) {
                if ($lastNum == true) {
                    $num = array_pop($out);
                    $out[] = $num . $value;
                } else {
                    $out[] = $value;
                    $lastNum = true;
                }
            } elseif ($value == "(") {
                $stack[] = $value;
                $lastNum = false;
            } elseif ($value == ")") {
                $bracket = false;
                while ($bracket != true)
                {
                    $op = array_pop($stack);

                    if ($op == "(") {
                        $bracket = true;
                    } else {
                        $out[] = $op;
                    }
                }

                $lastNum = false;
            }

        }

        $stack1 = $stack;
        $rpn = $out;

        while ($stack_el = array_pop($stack1)) {
            $rpn[] = $stack_el;
        }

        $rpn_str = implode(" ", $rpn);

        return $rpn_str;
    }

    /**
     * @param $str
     * @return mixed
     * @throws \Exception
     */
    protected function calc($str)
    {
        if (sizeof(explode(' ', $str)) < 2) {
            throw new InvalidArgumentException("Not enough data to count!");
        }

        $stack = [];

        $token = strtok($str, ' ');

        while ($token !== false)
        {
            if (in_array($token, ['*', '/', '+', '-', '^']))
            {
                if (count($stack) < 2) {
                    throw new InvalidArgumentException("Not enough data in stack for operation '$token'");
                }

                $b = array_pop($stack);
                $a = array_pop($stack);

                if ($token == '/' && $b == 0) {
                    throw new \DivisionByZeroError("Division by zero");
                }

                switch ($token) {
                    case '*': $res = $a*$b; break;
                    case '/': $res = $a/$b; break;
                    case '+': $res = $a+$b; break;
                    case '-': $res = $a-$b; break;
                    case '^': $res = pow($a,$b); break;
                }
                array_push($stack, $res);
            } elseif (is_numeric($token)) {
                array_push($stack, $token);
            } else {
                throw new InvalidArgumentException("Invalid character in expression: $token");
            }

            $token = strtok(' ');
        }

        if (count($stack) > 1) {
            throw new InvalidArgumentException("The number of operators does not match the number of operands");
        }

        return array_pop($stack);
    }
}