<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use App\Document\Site;
use App\Document\Page;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ODM\MongoDB\DocumentManager;

class SiteController extends AbstractController
{

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    public function index()
    {
        $site = new Site();
        $site->setName('A Foo Bar');

        $this->dm->persist($site);

        $page = new Page();
        $page->setName('A Foo Bar');
        $page->setSite($site);
        $this->dm->persist($page);

        $this->dm->flush();

        $number = random_int(0, 100);

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }
}