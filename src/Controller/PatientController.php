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

            // Check if the patient already exists by checking a unique identifier (e.g., JMBG)
            $existingPatient = Patient::getByJmbg($formData['jmbg'], 1);
            if ($existingPatient instanceof Patient) {
                // Update existing patient
                $existingPatient->setFirstname($formData['firstname']);
                $existingPatient->setLastname($formData['lastname']);
                $existingPatient->setDescription($formData['description']);
                $existingPatient->setPregled($formData['status']);
                $existingPatient->setCity($formData['city']);
                // Update other fields as needed

                // Save changes
                $existingPatient->save();

                $this->addFlash('success', $translator->trans('general.patient-updated'));
            } else {
                // Create new patient with a unique key
                $key = $formData['firstname'] . $formData['lastname'] . '_' . time();
                $patient = new Patient();
                $patient->setParent(Service::createFolderByPath('/patients/new'));
                $patient->setKey($key);
                $patient->setFirstname($formData['firstname']);
                $patient->setLastname($formData['lastname']);
                $patient->setDescription($formData['description']);
                $patient->setPregled($formData['status']);
                $patient->setJmbg($formData['jmbg']);
                $patient->setCity($formData['city']);
                // Set other fields as needed

                // Save changes
                $patient->save();

                $this->addFlash('success', $translator->trans('general.patient-submitted'));
            }

            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        return $this->render('error/404.html.twig');
    }

    private function validateFormData(array $formData): bool
    {

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

    public function edit(int $id): Response
    {
        // Fetch patient data based on the $id
        $patient = Patient::getById($id);

        if (!$patient instanceof Patient) {
            // Handle the case where the patient with the given $id is not found
            throw $this->createNotFoundException('Patient not found');
        }

        // Render the patient profile template with the patient data
        return $this->render('profile/patient.html.twig', ['patient' => $patient]);
    }



}
