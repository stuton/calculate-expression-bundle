<?php

namespace stuton\CalculateExpressionBundle\Entity;

class WolframAlphaAdapter implements CalculateExpressionInterface
{
    protected $wolframAlpha;

    public function __construct(WolframAlphaInterface $wolframAlpha)
    {
        $this->wolframAlpha = $wolframAlpha;
    }

    public function calculate(string $expression)
    {
        $this->wolframAlpha->getResults($expression);
    }
}