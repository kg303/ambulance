<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Attribute\Template;


class RegisterController extends FrontendController
{
    #[Template('account/register.html.twig')]
    public function registerAction(Request $request)
    {
        return [];
    }

}
