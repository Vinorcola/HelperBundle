<?php

namespace Vinorcola\HelperBundle;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

abstract class Controller extends BaseController
{
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
        if (!$this->container->has('translator')) {
            throw new LogicException('Translator service must be registered as a public service.');
        }

        $form->addError(new FormError(
            $this->container->get('translator')->trans($messageKey, $messageParameters, 'validators'),
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
        if (!$this->container->has('translator')) {
            throw new LogicException('Translator service must be registered as a public service.');
        }

        $this->addFlash($type, $this->container->get('translator')->trans($messageKey, $messageParameters));
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
