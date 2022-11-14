<?php

namespace Vinorcola\HelperBundle\Model;

use Symfony\Component\Translation\Exception\InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationModel
{
    public const PLURAL_PARAMETER = 'count';

    /**
     * @var string
     */
    private $attributePrefix;

    /**
     * TranslationModel constructor.
     *
     * @param TranslatorInterface $translator
     * @param RouteNamespaceModel $routeNamespaceModel
     * @param string              $attributePrefix
     * @param string              $separator
     */
    public function __construct(
        private TranslatorInterface $translator,
        private RouteNamespaceModel $routeNamespaceModel,
        string $attributePrefix = 'attribute',
        private string $separator = '.'
    ) {
        $this->attributePrefix = $attributePrefix . $separator;
    }

    /**
     * Translate a key.
     *
     * The key may be relative (e.g. "myKey"). It will be prepended with the route name (e.g. "myRoute.myKey").
     * The key may be absolute (e.g. "=myNamespace.myKey" which will be interpreted as "myNamespace.myKey").
     * The key can require plural form (e.g. "myKey+" or "=myNamespace.myKey+"). You must then provide a "count"
     * parameter.
     *
     * @param string      $key
     * @param string[]    $parameters
     * @param string|null $domain
     * @return string
     */
    public function tr(string $key, array $parameters = [], string $domain = null): string
    {
        if ($this->doesKeyRequirePlural($key) && !\array_key_exists('count', $parameters)) {
            throw new InvalidArgumentException('Plural key require a "count" parameter.');
        }

        return $this->translate(
            $this->resolveMessage($key),
            $parameters,
            $domain
        );
    }

    /**
     * Translate an entity attribute.
     *
     * @param string      $attribute
     * @param string      $entity
     * @param string[]    $parameters
     * @param string|null $domain
     * @return string
     */
    public function tra(string $attribute, string $entity, array $parameters = [], string $domain = null): string
    {
        return $this->translate(
            $this->attributePrefix . $entity . $this->separator . $attribute,
            $parameters,
            $domain
        );
    }

    /**
     * Translate a message.
     *
     * The parameters' names will be wrapped by percent sign if they are not already.
     *
     * @param string      $message
     * @param string[]    $parameters
     * @param string|null $domain
     * @return string
     */
    private function translate(string $message, array $parameters = [], string $domain = null): string
    {
        return $this->translator->trans($message, $this->resolveParameters($parameters), $domain);
    }

    /**
     * Translate a message with a plural parameter.
     *
     * The parameters' names will be wrapped by percent sign if they are not already.
     *
     * @param string      $key
     * @param int         $count
     * @param string[]    $parameters
     * @param string|null $domain
     * @return string
     */
    private function translatePlural(string $key, int $count, array $parameters = [], string $domain = null): string
    {
        return $this->translator->trans($key, $this->resolveParameters(\array_merge($parameters, [
            'count' => $count,
        ])), $domain);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isKeyAbsolute(string $key): bool
    {
        return $key[0] === '=';
    }

    /**
     * @param string $key
     * @return bool
     */
    public function doesKeyRequirePlural(string $key): bool
    {
        return mb_substr($key, -1) === '+';
    }

    /**
     * @param string $key
     * @return string
     */
    public function resolveMessage(string $key): string
    {
        $resolvedKey = $this->isKeyAbsolute($key) ?
            mb_substr($key, 1) :
            $this->routeNamespaceModel->getFullNamespace() . $key;

        if ($this->doesKeyRequirePlural($key)) {
            $resolvedKey = mb_substr($resolvedKey, 0, -1);
        }

        return $resolvedKey;
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
