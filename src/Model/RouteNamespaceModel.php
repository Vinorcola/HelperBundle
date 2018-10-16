<?php

namespace Vinorcola\HelperBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;

class RouteNamespaceModel
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $separator;

    /**
     * RouteNamespaceModel constructor.
     *
     * @param RequestStack $requestStack
     * @param string       $separator
     */
    public function __construct(RequestStack $requestStack, string $separator = '.')
    {
        $this->requestStack = $requestStack;
        $this->separator = $separator;
    }

    /**
     * Return the base route namespace.
     *
     * @return string
     */
    public function getBaseNamespace(): string
    {
        $routeName = $this->requestStack->getMasterRequest()->get('_route');

        return mb_substr($routeName, 0, mb_strpos($routeName, $this->separator) + 1);
    }

    /**
     * Return the current route namespace.
     *
     * @return string
     */
    public function getFullNamespace(): string
    {
        return $this->requestStack->getMasterRequest()->get('_route') . $this->separator;
    }
}
