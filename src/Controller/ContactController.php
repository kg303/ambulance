<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Attribute\Template;


class ContactController extends FrontendController
{
    #[Template('contact/contact.html.twig')]
    public function contactAction(Request $request)
    {
        return [];
    }

}
