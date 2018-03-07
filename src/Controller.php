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
}
