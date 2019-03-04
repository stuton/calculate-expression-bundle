<?php

namespace stuton\CalculateExpressionBundle\Entity;

interface WolframAlphaInterface
{
    public function getResults(string $expression);
}