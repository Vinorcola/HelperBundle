<?php

namespace Vinorcola\HelperBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Vinorcola\HelperBundle\DependencyInjection\VinorcolaHelperExtension;

class VinorcolaHelperBundle extends Bundle
{
    /**
     * {@inheritdoc}
     * @return VinorcolaHelperExtension
     */
    public function getContainerExtension()
    {
        return new VinorcolaHelperExtension();
    }
}
