<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Doctor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pimcore\Translation\Translator;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Pimcore\Model\DataObject\Service;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Examinations;

class DoctorController extends FrontendController
{
    //DOCTOR SUBMIT FORM ACTION
    /**
     * @throws \Exception
     */
    public function doctorSubmitAction(Request $request, Translator $translator): Response
    {

        if ($request->isMethod('POST')) {
            $formData = $request->request->all();

            if ($this->validateFormData($formData)) {
                // Check if the doctor already exists
                $existingDoctor = Doctor::getByUsername($formData['username'], 1);

                if ($existingDoctor instanceof Doctor) {
                    // Update existing doctor
                    $existingDoctor->setFirstname($formData['firstname']);
                    $existingDoctor->setLastname($formData['lastname']);
                    $existingDoctor->setUsername($formData['username']);
                    $existingDoctor->setPassword($formData['password']);
                    $existingDoctor->setDoctorType($formData['type']);
                    $existingDoctor->save();

                    $this->addFlash('success', $translator->trans('general.doctor-updated'));
                } else {
                    // Create a new doctor
                    $newDoctor = new Doctor();
                    $newDoctor->setParent(Service::createFolderByPath('/doctors'));
                    $newDoctor->setKey($formData['username']);
                    $newDoctor->setFirstname($formData['firstname']);
                    $newDoctor->setLastname($formData['lastname']);
                    $newDoctor->setUsername($formData['username']);
                    $newDoctor->setPassword($formData['password']);
                    $newDoctor->setDoctorType($formData['type']);
                    $newDoctor->save();

                    $this->addFlash('success', $translator->trans('general.doctor-submitted'));
                }

                return $this->redirect($request->server->get('HTTP_REFERER'));
            } else {
                $this->addFlash('error', $translator->trans('general.form-validation-failed'));
            }
        }

        return $this->render('error/404.html.twig');

    }

    private function validateFormData(array $formData): bool
    {

        return isset($formData['firstname']) && isset($formData['lastname']);
    }

    //DOCTOR LISTING ACTION FOR DOCTOR TABLE
    public function doctorAction(Request $request)
    {
        $doctors = Doctor::getList();

        return $this->render('tables/data.html.twig', [
            'doctors' => $doctors
        ]);
    }

    //DOCTOR EDIT ACTION FOR DOCTORS
    public function edit(int $id): Response
    {
        $doctor = Doctor::getById($id);

        if (!$doctor instanceof Doctor) {
            throw $this->createNotFoundException('Doctor not found');
        }

        return $this->render('profile/doctor.html.twig', ['doctor' => $doctor]);
    }

    //DOCTOR REMOVE ACTION FOR DOCTORS
    /**
     * @Route("/Doktori/remove/{id}", name="doctor_remove")
     * @throws \Exception
     */
    public function remove($id): Response
    {
        $doctor = Doctor::getById($id);

        if (!$doctor instanceof Doctor) {
            throw $this->createNotFoundException('Doctor not found');
        }

        $doctor->delete();

        return $this->redirectToRoute('doctors_list');
    }

}
