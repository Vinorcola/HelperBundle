<?php

namespace Vinorcola\HelperBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vinorcola\HelperBundle\DependencyInjection\VinorcolaHelperExtension;

class VinorcolaHelperBundle extends Bundle
{
    /**
     * {@inheritdoc}
     * @return VinorcolaHelperExtension
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new VinorcolaHelperExtension();
    }
}
