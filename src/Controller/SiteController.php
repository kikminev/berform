<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;
use App\Document\Site;
use App\Document\Page;

class SiteController extends AbstractController
{
    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    //
    public function home()
    {
        //        $site = new Site();
        //        $site->setName('A Foo Bar');
        //
        //        $this->dm->persist($site);
        //
        //        $page = new Page();
        //        $page->setName('A Foo Bar');
        //        $page->setSite($site);
        //        $this->dm->persist($page);
        //
        //        $this->dm->flush();

        return new Response(
            '<html><body>Welcome</body></html>'
        );
    }
}
