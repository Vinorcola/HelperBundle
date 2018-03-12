<?php

namespace Vinorcola\HelperBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationModel
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * TranslationModel constructor.
     *
     * @param TranslatorInterface $translator
     * @param RequestStack        $requestStack
     */
    public function __construct(TranslatorInterface $translator, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
    }

    /**
     * Translate a key prefixed with the current route name.
     *
     * @param string   $key
     * @param string[] $parameters
     * @return string
     */
    public function tr(string $key, array $parameters = []): string
    {
        return $this->translate(
            $this->requestStack->getCurrentRequest()->get('_route') . '.' . $key,
            $parameters
        );
    }

    /**
     * Translate a key prefixed with the current route name.
     *
     * @param string   $key
     * @param int      $nb
     * @param string[] $parameters
     * @return string
     */
    public function trPlural(string $key, int $nb, array $parameters = []): string
    {
        return $this->translatePlural(
            $this->requestStack->getCurrentRequest()->get('_route') . '.' . $key,
            $nb,
            $parameters
        );
    }

    /**
     * Translate an entity attribute.
     *
     * @param string   $attribute
     * @param string   $entity
     * @param string[] $parameters
     * @return string
     */
    public function tra(string $attribute, string $entity, array $parameters = []): string
    {
        return $this->translate('attribute.' . $entity . '.' . $attribute, $parameters);
    }

    /**
     * Translate a given key with given parameters.
     *
     * The parameters' names will be wrapped by percent sign if they are not already.
     *
     * @param string   $key
     * @param string[] $parameters
     * @param string   $domain
     * @return string
     */
    public function translate(string $key, array $parameters = [], string $domain = 'messages'): string
    {
        $securedParameters = [];
        foreach ($parameters as $name => $value) {
            $name = (string) $name;
            if ($name[0] !== '%' || $name[mb_strlen($name) - 1] !== '%') {
                $securedParameters['%' . $name . '%'] = $value;
            } else {
                $securedParameters[$name] = $value;
            }
        }

        return $this->translator->trans($key, $securedParameters, $domain);
    }

    /**
     * Translate a given key with given parameters.
     *
     * The parameters' names will be wrapped by percent sign if they are not already.
     *
     * @param string   $key
     * @param int      $nb
     * @param string[] $parameters
     * @param string   $domain
     * @return string
     */
    public function translatePlural(string $key, int $nb, array $parameters = [], string $domain = 'messages'): string
    {
        $securedParameters = [];
        foreach ($parameters as $name => $value) {
            $name = (string) $name;
            if ($name[0] !== '%' || $name[mb_strlen($name) - 1] !== '%') {
                $securedParameters['%' . $name . '%'] = $value;
            } else {
                $securedParameters[$name] = $value;
            }
        }

        return $this->translator->transChoice($key, $nb, $securedParameters, $domain);
    }
}
