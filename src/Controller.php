<?php

namespace Vinorcola\HelperBundle;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Vinorcola\HelperBundle\Model\TranslationModel;

abstract class Controller extends AbstractController
{
    /**
     * Controller constructor.
     *
     * @param TranslationModel $translationModel
     * @param ManagerRegistry  $entityRegistry
     */
    public function __construct(
        protected TranslationModel $translationModel,
        protected ManagerRegistry $entityRegistry,
    ) {}

    /**
     * Save the database.
     */
    protected function saveDatabase(): void
    {
        $this->entityRegistry->getManager()->flush();
    }

    /**
     * Add an error in the given form part.
     *
     * @param FormInterface $form
     * @param string        $messageKey
     * @param string[]      $messageParameters
     * @param string        $domain
     */
    protected function addFormError(FormInterface $form, string $messageKey, array $messageParameters = [], string $domain = 'validators'): void
    {
        $form->addError(new FormError(
            $this->translationModel->tr($messageKey, $messageParameters, $domain),
            $messageKey,
            $messageParameters,
            $this->translationModel->doesKeyRequirePlural($messageKey) ?
                $messageParameters[TranslationModel::PLURAL_PARAMETER] :
                null
        ));
    }

    /**
     * Add a flash message.
     *
     * @param string   $type
     * @param string   $messageKey
     * @param string[] $messageParameters
     * @param string   $domain
     */
    protected function addMessage(string $type, string $messageKey, array $messageParameters = [], string $domain = 'messages'): void
    {
        $this->addFlash($type, $this->translationModel->tr($messageKey, $messageParameters, $domain));
    }

    /**
     * Add an error flash message.
     *
     * @param string[] $messageParameters
     * @param bool     $bootstrapTheme If true, set a "danger" flash instead of an "error" flash.
     */
    protected function addErrorMessage(array $messageParameters = [], bool $bootstrapTheme = false): void
    {
        $this->addMessage($bootstrapTheme ? 'danger' : 'error', 'error', $messageParameters);
    }

    /**
     * Add an info flash message.
     *
     * @param string[] $messageParameters
     */
    protected function addInfoMessage(array $messageParameters = []): void
    {
        $this->addMessage('info', 'info', $messageParameters);
    }

    /**
     * Add a success flash message.
     *
     * @param string[] $messageParameters
     */
    protected function addSuccessMessage(array $messageParameters = []): void
    {
        $this->addMessage('success', 'success', $messageParameters);
    }

    /**
     * Add a warning flash message.
     *
     * @param string[] $messageParameters
     */
    protected function addWarningMessage(array $messageParameters = []): void
    {
        $this->addMessage('warning', 'warning', $messageParameters);
    }
}
