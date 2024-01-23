<?php

namespace App\Controller;

use Pimcore\Model\DataObject\Doctor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    /**
     * @Route("/Prijava", name="Prijava")
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    public function loginAction(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $errormessage = null;
        $successMessage = null;

        if ($request->isMethod('POST')) {
            try {
                $providedUsername = $request->request->get('username');
                $providedPassword = $request->request->get('password');

                $doctor = Doctor::getByUsername($providedUsername,1);

//                dd(password_verify($providedPassword, $doctor->getPassword()));
//                foreach ($doctors as $doctor) {
//                    $username = $doctor->getUsername();
//                    $password = $doctor->getPassword();

//                    if ($doctorusername === $providedUsername) {

                        if ($passwordHasher->isPasswordValid($doctor, $providedPassword)) {
                            $successMessage = 'Successfully logged in!';
                            return $this->redirectToRoute('patient_list');
                        }


                throw new BadCredentialsException('Invalid username or password');
            } catch (BadCredentialsException $exception) {

                $errormessage = 'Invalid username or password';
            }
        }


        $successMessage = $request->query->get('successMessage');

        return $this->render('account/login.html.twig', [
            'errormessage' => $errormessage,
            'successMessage' => $successMessage,
        ]);
    }
}
