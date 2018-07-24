<?php

namespace Vinorcola\HelperBundle\Model;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;

class TranslationModel
{
    public const ENTITY_ATTRIBUTE_PREFIX = 'attribute.';
    public const PLURAL_PARAMETER = 'pluralParam';

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
     * Translate a key.
     *
     * The key may be relative (e.g. "myKey"). It will be prepended with the route name (e.g. "myRoute.myKey").
     * The key may be absolute (e.g. "=myNamespace.myKey" which will be interpreted as "myNamespace.myKey").
     * The key can require plural form (e.g. "myKey+" or "=myNamespace.myKey+"). You must then provide a "pluralParam"
     * parameter.
     *
     * @param string   $key
     * @param string[] $parameters
     * @param string   $domain
     * @return string
     */
    public function tr(string $key, array $parameters = [], string $domain = 'messages'): string
    {
        return $this->doesKeyRequirePlural($key) ?
            $this->translatePlural(
                $this->resolveMessage($key),
                $parameters[self::PLURAL_PARAMETER],
                $parameters,
                $domain
            ) :
            $this->translate(
                $this->resolveMessage($key),
                $parameters,
                $domain
            );
    }

    /**
     * Translate a pluralized key.
     *
     * The key may be relative (e.g. "myKey"). It will be prepended with the route name (e.g. "myRoute.myKey").
     * The key may be absolute (e.g. "=myNamespace.myKey" which will be interpreted as "myNamespace.myKey").
     *
     * @param string   $key
     * @param int      $nb
     * @param string[] $parameters
     * @param string   $domain
     * @return string
     */
    public function trPlural(string $key, int $nb, array $parameters = [], string $domain = 'messages'): string
    {
        return $this->translatePlural(
            $this->resolveMessage($key),
            $nb,
            $parameters,
            $domain
        );
    }

    /**
     * Translate an entity attribute.
     *
     * @param string   $attribute
     * @param string   $entity
     * @param string[] $parameters
     * @param string   $domain
     * @return string
     */
    public function tra(string $attribute, string $entity, array $parameters = [], string $domain = 'messages'): string
    {
        return $this->translate(
            self::ENTITY_ATTRIBUTE_PREFIX . $entity . '.' . $attribute,
            $parameters,
            $domain
        );
    }

    /**
     * Translate a message.
     *
     * The parameters' names will be wrapped by percent sign if they are not already.
     *
     * @param string   $message
     * @param string[] $parameters
     * @param string   $domain
     * @return string
     */
    public function translate(string $message, array $parameters = [], string $domain = 'messages'): string
    {
        return $this->translator->trans($message, $this->resolveParameters($parameters), $domain);
    }

    /**
     * Translate a message with a plural parameter.
     *
     * The parameters' names will be wrapped by percent sign if they are not already.
     *
     * @param string   $key
     * @param int      $pluralParameter
     * @param string[] $parameters
     * @param string   $domain
     * @return string
     */
    public function translatePlural(string $key, int $pluralParameter, array $parameters = [], string $domain = 'messages'): string
    {
        return $this->translator->transChoice($key, $pluralParameter, $this->resolveParameters($parameters), $domain);
    }

    /**
     * @param string $key
     * @return bool
     */
    private function isKeyAbsolute(string $key): bool
    {
        return $key[0] === '=';
    }

    /**
     * @param string $key
     * @return bool
     */
    private function doesKeyRequirePlural(string $key): bool
    {
        return substr($key, -1) === '+';
    }

    /**
     * @param string $key
     * @return bool
     */
    private function resolveMessage(string $key): bool
    {
        return rtrim(
            $this->isKeyAbsolute($key) ?
                substr($key, 1) :
                $this->requestStack->getCurrentRequest()->get('_route') . '.' . $key,
            '+'
        );
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function resolveParameters(array $parameters): array
    {
        $resolvedParameters = [];
        foreach ($parameters as $name => $value) {
            $name = (string) $name;
            if (mb_strlen($name) > 0 && ($name[0] !== '%' || $name[mb_strlen($name) - 1] !== '%')) {
                $newName = '%' . $name . '%';
                if (key_exists($newName, $parameters)) {
                    // Key already exists, we fall back to original key.
                    $newName = $name;
                }
                $resolvedParameters[$newName] = $value;
            } else {
                $resolvedParameters[$name] = $value;
            }
        }

        return $resolvedParameters;
    }
}
