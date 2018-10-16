<?php

namespace Vinorcola\HelperBundle\Twig;

use DateTimeInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Vinorcola\HelperBundle\Model\TranslationModel;
use Vinorcola\HelperBundle\Model\UrlGeneratorModel;

class Extension extends AbstractExtension
{
    /**
     * @var TranslationModel
     */
    private $translationModel;

    /**
     * @var UrlGeneratorModel
     */
    private $urlGeneratorModel;

    /**
     * Extension constructor.
     *
     * @param TranslationModel  $translationModel
     * @param UrlGeneratorModel $urlGeneratorModel
     */
    public function __construct(TranslationModel $translationModel, UrlGeneratorModel $urlGeneratorModel)
    {
        $this->translationModel = $translationModel;
        $this->urlGeneratorModel = $urlGeneratorModel;
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
                'is_safe' => [ 'html' ],
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
            new TwigFunction('rpath', [ $this, 'rpath' ]),
        ];
    }

    /**
     * Generate a Url from a relative route name.
     *
     * @param string $name
     * @param array  $parameters
     * @param bool   $relative
     * @return string
     */
    public function rpath(string $name, array $parameters = array(), bool $relative = false): string
    {
        return $this->urlGeneratorModel->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Translate a key prefixed with the current route name.
     *
     * @param string      $key
     * @param string[]    $parameters
     * @param string|null $domain
     * @return string
     */
    public function tr(string $key, array $parameters = [], string $domain = null): string
    {
        return $this->translationModel->tr($key, $parameters, $domain);
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
        return $this->translationModel->tra($attribute, $entity, $parameters, $domain);
    }

    /**
     * Translate page title.
     *
     * @param string[]    $parameters
     * @param string|null $domain
     * @return string
     */
    public function pageTitle(array $parameters = [], string $domain = null): string
    {
        return $this->translationModel->tr('title', $parameters, $domain);
    }

    /**
     * Output the value for javascript.
     *
     * @param mixed $value
     * @return string
     */
    public function toJs($value): string
    {
        if ($value instanceof DateTimeInterface) {
            return 'new Date(' . $value->format('Y') . ', ' . ($value->format('m') - 1) . ', ' . $value->format('d') . ')';
        }

        return json_encode($value);
    }
}
