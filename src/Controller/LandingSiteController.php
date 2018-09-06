<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;

class LandingSiteController extends AbstractController
{
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function home()
    {
        return new Response(
            '<html><body>Welcome</body></html>'
        );
    }
}
