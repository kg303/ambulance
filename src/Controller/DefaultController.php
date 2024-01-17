<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Twig\Attribute\Template;

class DefaultController extends FrontendController
{

    #[Template('content/main.html.twig')]
    public function defaultAction(Request $request)
    {
        return [];
    }


    #[Template('includes/footer.html.twig')]
    public function footerAction(Request $request)
    {
        return [];
    }
}
