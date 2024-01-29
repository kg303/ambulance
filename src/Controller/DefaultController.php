<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Twig\Attribute\Template;
use Pimcore\Model\DataObject\Patient;
use Pimcore\Model\DataObject\Examinations;
use Pimcore\Model\DataObject\Service;
use Pimcore\Model\DataObject\Patient\Listing as PatientListing;
use Symfony\Component\Security\Core\Security;


class DefaultController extends FrontendController
{

    #[Template('home/main.html.twig')]
    public function defaultAction(Request $request, Security $security)
    {

            $patients = Patient::getList();

            $patientsExamined = iterator_to_array($patients);

            $totalPatientsCount = count($patientsExamined);

            $pregledanCount = count(array_filter($patientsExamined, function ($patient) {
                return $patient->getPregled() === 'Pregledan';
            }));

            $nepregledanCount = count(array_filter($patientsExamined, function ($patient) {
                return $patient->getPregled() === 'Ne pregledan';
            }));

            $umroCount = count(array_filter($patientsExamined, function ($patient) {
                return $patient->getPregled() === 'Umro';
            }));

            $pregledanPercentage = ($totalPatientsCount > 0) ? ($pregledanCount / $totalPatientsCount * 100) : 0;
            $nepregledanPercentage = ($totalPatientsCount > 0) ? ($nepregledanCount / $totalPatientsCount * 100) : 0;

            return $this->render('home/main.html.twig', [
                'patients' => $patients,
                'totalPatientsCount' => $totalPatientsCount,
                'pregledan' => $pregledanCount,
                'nepregledan' => $nepregledanCount,
                'pregledanPercentage' => $pregledanPercentage,
                'nepregledanPercentage' => $nepregledanPercentage,
                'umroPercentage' => $umroCount,

            ]);
        }




    #[Template('includes/footer.html.twig')]
    public function footerAction(Request $request)
    {
        return [];
    }
}
