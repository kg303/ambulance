<?php


namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Examinations;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Translation\Translator;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\Routing\Annotation\Route;
use Carbon\Carbon;
use Pimcore\Model\DataObject\Patient;
use Pimcore\Model\DataObject\Doctor;



class ExaminationController extends FrontendController
{

    /**
     * @throws \Exception
     */
    public function examinationActionForm(Request $request, Translator $translator): Response
    {


        if ($request->isMethod('POST')) {
            $formData = $request->request->all();

            $existingPatientKey = $formData['firstname'].$formData['lastname'];
            $existingPatient = Patient::getByPath('/patients/new/' . $existingPatientKey);

            if ($existingPatient instanceof Patient) {
                // Update existing patient
                $patient = $existingPatient;
                $patient->setFirstname($formData['firstname']);
                $patient->setLastname($formData['lastname']);
                $patient->setDescription($formData['description']);
                $patient->setPregled($formData['status']);

            } else {
                // Create new patient
                $patient = new Patient();
                $patient->setParent(Service::createFolderByPath('/patients/new'));
                $patient->setKey($formData['firstname'].$formData['lastname']);
                $patient->setFirstname($formData['firstname']);
                $patient->setLastname($formData['lastname']);
                $patient->setDescription($formData['description']);
                $patient->setPregled($formData['status']);
                $patient->setJmbg($formData['jmbg']);
                $patient->setCity($formData['city']);
            }

            $patient->save();

            // Now handle examinations
            $examinationKey = $formData['firstname'].$formData['lastname'];
            $examination = Examinations::getByPath('/examinations/' . $examinationKey);

            if (!$examination instanceof Examinations) {
                // If examination doesn't exist, create a new one
                $examination = new Examinations();
                $examination->setParent(Service::createFolderByPath('/examinations'));
                $examination->setKey($examinationKey);
            }

            // Update or create the examination
            $examination->setFirstname($formData['firstname']);
            $examination->setLastname($formData['lastname']);
            $examination->setDoktor($formData['doctor']);
            $examination->setDiagnosis($formData['description']);
            $examinationDate = Carbon::parse($formData['date']);
            $examination->setExaminationDate($examinationDate);
            $examination->setPregled($formData['status']);

            $examination->setPatientrelation($patient);

            $examination->save();

            $this->addFlash('success', $translator->trans('general.examination-submitted'));

            return $this->redirect($request->server->get('HTTP_REFERER'));
        } else {
            $this->addFlash('error', $translator->trans('general.form-validation-failed'));
        }


        return $this->render('error/404.html.twig');

    }

    private function validateFormData(array $formData): bool
    {

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
