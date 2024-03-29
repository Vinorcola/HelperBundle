<?php

namespace Vinorcola\HelperBundle\Model;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlGeneratorModel
{
    /**
     * UrlGeneratorModel constructor.
     *
     * @param RouteNamespaceModel   $routeNamespaceModel
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private RouteNamespaceModel $routeNamespaceModel,
    ) {}

    /**
     * @param string $name
     * @param array  $parameters
     * @param int    $referenceType
     * @return string
     */
    public function generate(string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->urlGenerator->generate($this->routeNamespaceModel->getBaseNamespace() . $name, $parameters, $referenceType);
    }
}
