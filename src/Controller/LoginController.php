<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Attribute\Template;


class LoginController extends FrontendController
{
    #[Template('account/login.html.twig')]
    public function loginAction(Request $request)
    {
        return [];
    }

}
