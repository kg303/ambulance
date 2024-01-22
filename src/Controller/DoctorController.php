<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Doctor; // Make sure to import your DataObject class
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Translation\Translator;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Examinations;

class DoctorController extends FrontendController
{

    public function doctorSubmitAction(Request $request, Translator $translator): Response
    {


        // Handle form submission manually
        if ($request->isMethod('POST')) {
            $formData = $request->request->all();
            // Validate form data manually
            if ($this->validateFormData($formData)) {
                // Process and save data
                $doctors = new Doctor();
                $doctors->setParent(Service::createFolderByPath('/doctors'));
                $doctors->setKey($formData['username']);
                $doctors->setFirstname($formData['firstname']);
                $doctors->setLastname($formData['lastname']);
                $doctors->setUsername($formData['username']);
                $doctors->setPassword($formData['password']);
                $doctors->setDoctorType($formData['type']);
                $doctors->save();

                $this->addFlash('success', $translator->trans('general.examination-submitted'));

                return $this->redirect($request->server->get('HTTP_REFERER'));
            } else {
                $this->addFlash('error', $translator->trans('general.form-validation-failed'));
            }
        }

        return $this->render('error/404.html.twig');

    }

    private function validateFormData(array $formData): bool
    {
        // Perform manual validation logic
        // Return true if data is valid, false otherwise
        return isset($formData['firstname']) && isset($formData['lastname']);
    }


    public function doctorAction(Request $request)
    {
        $doctors = Doctor::getList();

        return $this->render('tables/data.html.twig', [
            'doctors' => $doctors
        ]);
    }

}
