<?php

namespace Vinorcola\HelperBundle;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Vinorcola\HelperBundle\Model\TranslationModel;

abstract class Controller extends BaseController
{
    /**
     * @var TranslationModel
     */
    private $translationModel;

    /**
     * Controller constructor.
     *
     * @param TranslationModel $translationModel
     */
    public function __construct(TranslationModel $translationModel)
    {
        $this->translationModel = $translationModel;
    }

    /**
     * Save the database.
     */
    protected function saveDatabase(): void
    {
        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * Add an error in the given form part.
     *
     * @param FormInterface $form
     * @param string        $messageKey
     * @param string[]      $messageParameters
     */
    protected function addFormError(FormInterface $form, string $messageKey, array $messageParameters = []): void
    {
        $form->addError(new FormError(
            $this->translationModel->translate($messageKey, $messageParameters, 'validators'),
            $messageKey,
            $messageParameters
        ));
    }

    /**
     * Add a flash message.
     *
     * @param string   $type
     * @param string   $messageKey
     * @param string[] $messageParameters
     */
    protected function addMessage(string $type, string $messageKey, array $messageParameters = []): void
    {
        $this->addFlash($type, $this->translationModel->tr($messageKey, $messageParameters));
    }

    /**
     * Add an error flash message.
     *
     * @param string   $messageKey
     * @param string[] $messageParameters
     */
    protected function addErrorMessage(string $messageKey, array $messageParameters = []): void
    {
        $this->addMessage('error', $messageKey, $messageParameters);
    }

    /**
     * Add a success flash message.
     *
     * @param string   $messageKey
     * @param string[] $messageParameters
     */
    protected function addSuccessMessage(string $messageKey, array $messageParameters = []): void
    {
        $this->addMessage('success', $messageKey, $messageParameters);
    }
}
