<?php

// src/Controller/PatientController.php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Examinations; // Make sure to import your DataObject class
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Translation\Translator;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\Routing\Annotation\Route;
use Carbon\Carbon;
use Pimcore\Model\DataObject\Doctor; // Import the Doctor class


class ExaminationController extends FrontendController
{

    /**
     * @throws \Exception
     */
    public function examinationActionForm(Request $request, Translator $translator): Response
    {


        // Handle form submission manually
        if ($request->isMethod('POST')) {
            $formData = $request->request->all();
            // Validate form data manually
            if ($this->validateFormData($formData)) {
                // Process and save data
                $examination = new Examinations();
                $examination->setParent(Service::createFolderByPath('/examinations'));
                $examination->setKey($formData['firstname'].$formData['lastname']);
                $examination->setFirstname($formData['firstname']);
                $examination->setLastname($formData['lastname']);
                $examination->setDoktor($formData['doctor']);
                $examination->setDiagnosis($formData['diagnosis']);
                $examinationDate = Carbon::parse($formData['date']);
                $examination->setExaminationDate($examinationDate);
                $examination->save();

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


    public function examinationListingAction(Request $request): Response
    {
        $examination = Examinations::getList();

        return $this->render('tables/data.html.twig', [
            'examinations' => $examination,
        ]);
    }




}
