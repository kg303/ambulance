<?php


namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Patient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Translation\Translator;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Examinations;
use Doctrine\ORM\EntityManagerInterface;


class PatientController extends FrontendController
{

    /**
     * @throws \Exception
     */
    public function submitFormAction(Request $request, Translator $translator): Response
    {
        if ($request->isMethod('POST')) {
            $formData = $request->request->all();

            $existingPatient = Patient::getByJmbg($formData['jmbg'], 1);
            if ($existingPatient instanceof Patient) {
                $existingPatient->setFirstname($formData['firstname']);
                $existingPatient->setLastname($formData['lastname']);
                $existingPatient->setDescription($formData['description']);
                $existingPatient->setPregled($formData['status']);
                $existingPatient->setCity($formData['city']);

                $existingPatient->save();

                $this->addFlash('success', $translator->trans('general.patient-updated'));
            } else {
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
        $patient = Patient::getById($id);

        if (!$patient instanceof Patient) {
            throw $this->createNotFoundException('Patient not found');
        }

        return $this->render('profile/patient.html.twig', ['patient' => $patient]);
    }



    /**
     * @Route("/Pacijenti/remove/{id}", name="patient_remove")
     * @throws \Exception
     */
    public function remove($id): Response
    {
        $patient = Patient::getById($id);

        if (!$patient instanceof Patient) {
            throw $this->createNotFoundException('Patient not found');
        }

        $patient->delete();

        return $this->redirectToRoute('patient_list');
    }


    //FOR EXAMINED PATIENTS
    public function editExaminationsAction(int $id): Response
    {
        $examinations = Examinations::getById($id);

        if (!$examinations instanceof Examinations) {
            throw $this->createNotFoundException('Patient not found');
        }

        return $this->render('profile/examinations.html.twig', ['examination' => $examinations]);
    }



    /**
     * @Route("/Pacijenti/remove/{id}", name="patient_remove")
     * @throws \Exception
     */
    public function removeExaminationsAction($id): Response
    {
        $examinations = Examinations::getById($id);

        if (!$examinations instanceof Examinations) {
            throw $this->createNotFoundException('Patient not found');
        }

        $examinations->delete();

        return $this->redirectToRoute('patient_list');
    }


}
