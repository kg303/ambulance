<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Pimcore\Model\DataObject\Doctor;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{


    /**
     * @Route("/Registracija", name="Registracija")
     */
    public function registerAction(Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $firstname = $request->request->get('firstname');
            $lastname = $request->request->get('lastname');
            $password = $request->request->get('password');

            try {
                // Validate input
                if (empty($username) || empty($firstname) || empty($lastname) ||empty($password)) {
                    throw new \Exception('Username, firstname, lastname and password are required!');
                }

                $user = new Doctor();
                $user->setKey(\Pimcore\Model\Element\Service::getValidKey($username, 'object'));
                $user->setParentId(30);
                $user->setUsername($username);
                $user->setFirstname($firstname);
                $user->setLastname($lastname);

                $plaintextPassword = $request->request->get('password');

                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $plaintextPassword
                );

                $user->setPassword($hashedPassword);




                if (empty($user->getUsername()) || empty($user->getFirstname()) || empty($user->getLastname()) ||empty($user->getPassword())) {
                    throw new \Exception('Username, email, and password cannot be empty');
                }

                $user->save();

                return $this->redirectToRoute('patient_list');
            } catch (\Exception $e) {
                return $this->render('account/register.html.twig', ['error' => $e->getMessage()]);
            }
        }

        return $this->render('account/register.html.twig');
    }




}


