<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Attribute\Template;
use Pimcore\Model\DataObject\Doctor;

class DoctorController extends FrontendController
{

    public function doctorAction(Request $request)
    {
        $doctors = Doctor::getList();

        return $this->render('tables/data.html.twig', [
            'doctors' => $doctors
        ]);
    }

}
