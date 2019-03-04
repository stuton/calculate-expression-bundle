<?php

namespace stuton\CalculateExpressionBundle\Entity;

interface CalculateExpressionInterface
{
    public function calculate(string $expression);
}