<?php

namespace Vinorcola\HelperBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;

class RouteNamespaceModel
{
    /**
     * RouteNamespaceModel constructor.
     *
     * @param RequestStack $requestStack
     * @param string       $separator
     */
    public function __construct(private RequestStack $requestStack, private string $separator = '.') {}

    /**
     * Return the base route namespace.
     *
     * @return string
     */
    public function getBaseNamespace(): string
    {
        $routeName = $this->requestStack->getMainRequest()->get('_route');

        return mb_substr($routeName, 0, mb_strrpos($routeName, $this->separator) + 1);
    }

    /**
     * Return the current route namespace.
     *
     * @return string
     */
    public function getFullNamespace(): string
    {
        return $this->requestStack->getMainRequest()->get('_route') . $this->separator;
    }
}
