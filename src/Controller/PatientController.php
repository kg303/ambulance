<?php

// src/Controller/PatientController.php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Patient; // Make sure to import your DataObject class
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Translation\Translator;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Examinations;


class PatientController extends FrontendController
{

    /**
     * @throws \Exception
     */
    public function submitFormAction(Request $request, Translator $translator): Response
    {
        // Handle form submission manually
        if ($request->isMethod('POST')) {
            $formData = $request->request->all();
            // Validate form data manually
            if ($this->validateFormData($formData)) {
                // Process and save data
                $patient = new Patient();
                $patient->setParent(Service::createFolderByPath('/patients/new'));
                $patient->setKey($formData['firstname'].$formData['lastname']);
                $patient->setFirstname($formData['firstname']);
                $patient->setLastname($formData['lastname']);
                $patient->setDescription($formData['description']);

                $patient->setJmbg($formData['jmbg']);
                $patient->setCity($formData['city']);

                $patient->save();

                $this->addFlash('success', $translator->trans('general.patient-submitted'));

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


    public function listingAction(Request $request): Response
    {
        $patients = Patient::getList();
        $examinedPatients = Examinations::getList();

        return $this->render('tables/patients.html.twig', [
            'patients' => $patients,
            'examinedPatients' => $examinedPatients
        ]);
    }


}
