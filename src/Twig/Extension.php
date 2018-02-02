<?php

namespace Vinorcola\HelperBundle\Twig;

use DateTime;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Extension extends AbstractExtension
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
     * Extension constructor.
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
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('tr', [ $this, 'tr' ]),
            new TwigFilter('tra', [ $this, 'tra' ]),
            new TwigFilter('toJs', [ $this, 'toJs' ], [
                'is_safe' => [ 'js' ],
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('pageTitle', [ $this, 'pageTitle' ]),
        ];
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
        return $this->translateWithParameters(
            $this->requestStack->getCurrentRequest()->get('_route') . '.' . $key,
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
        return $this->translateWithParameters('attribute.' . $entity . '.' . $attribute, $parameters);
    }

    /**
     * Translate page title.
     *
     * @param string[] $parameters
     * @return string
     */
    public function pageTitle(array $parameters = []): string
    {
        return $this->tr('title', $parameters);
    }

    /**
     * Output the value for javascript.
     *
     * @param mixed $value
     * @return string
     */
    public function toJs($value): string
    {
        if ($value instanceof DateTime) {
            return 'new Date(' . $value->format('Y') . ', ' . ($value->format('m') - 1) . ', ' . $value->format('d') . ')';
        }

        return json_encode($value);
    }

    /**
     * Auto add percent sign around translation parameters.
     *
     * @param string $key
     * @param array  $parameters
     * @return string
     */
    private function translateWithParameters(string $key, array $parameters = []): string
    {
        $escapedParameters = [];
        foreach ($parameters as $name => $value) {
            $escapedParameters['%' . $name . '%'] = $value;
        }

        return $this->translator->trans($key, $escapedParameters);
    }
}
