<?php

namespace Vinorcola\HelperBundle;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    /**
     * Save the database.
     */
    protected function saveDatabase(): void
    {
        $this->getDoctrine()->getManager()->flush();
    }
}
