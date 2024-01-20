<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Attribute\Template;


class ProfileController extends FrontendController
{
    #[Template('profile/profile.html.twig')]
    public function profileAction(Request $request)
    {
        return [];
    }

}
