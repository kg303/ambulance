<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bridge\Twig\Attribute\Template;
use Pimcore\Model\DataObject\Doctor; // Import the Doctor class
use Pimcore\Model\DataObject\Patient;


class FormController extends FrontendController
{
    public function formAction(Request $request)
    {
        // Fetch the list of doctors
        $doctors = Doctor::getList();
        $patients = Patient::getList();

        // Extract doctor names to use as options in the select dropdown
        $doctorName = [];
        foreach ($doctors as $doctor) {
            $doctorName[$doctor->getFirstname()] = $doctor->getFirstname();
        }

        $patientName = [];
        foreach ($patients as $patient) {
            $patientName[$patient->getFirstname()] = $patient->getFirstname();
        }

        $patientLastName = [];
        foreach ($patients as $patient) {
            $patientLastName[$patient->getLastname()] = $patient->getLastname();
        }

        return $this->render('pregledi/index.html.twig', [
            'doctorNames' => $doctorName,
            'patientNames' => $patientName,
            'patientLastNames' => $patientLastName
        ]);
    }

}
