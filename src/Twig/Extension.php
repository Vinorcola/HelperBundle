<?php

namespace Vinorcola\HelperBundle\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Vinorcola\HelperBundle\Model\TranslationModel;

class Extension extends AbstractExtension
{
    /**
     * @var TranslationModel
     */
    private $translationModel;

    /**
     * Extension constructor.
     *
     * @param TranslationModel $translationModel
     */
    public function __construct(TranslationModel $translationModel)
    {
        $this->translationModel = $translationModel;
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
     * @param string   $domain
     * @return string
     */
    public function tr(string $key, array $parameters = [], string $domain = 'messages'): string
    {
        return $this->translationModel->tr($key, $parameters, $domain);
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
        return $this->translationModel->tra($attribute, $entity, $parameters, $domain);
    }

    /**
     * Translate page title.
     *
     * @param string[] $parameters
     * @param string   $domain
     * @return string
     */
    public function pageTitle(array $parameters = [], string $domain = 'messages'): string
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
        if ($value instanceof DateTime) {
            return 'new Date(' . $value->format('Y') . ', ' . ($value->format('m') - 1) . ', ' . $value->format('d') . ')';
        }

        return json_encode($value);
    }
}
