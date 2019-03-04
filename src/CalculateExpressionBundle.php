<?php

namespace stuton\CalculateExpressionBundle;

use stuton\CalculateExpressionBundle\Entity\CalculateExpressionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CalculateExpressionBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->registerForAutoconfiguration(CalculateExpressionInterface::class)->addTag('calculate.expression.service');
    }
}