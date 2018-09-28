<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Setup\CreateAdminData;
use App\Form\Setup\CreateAdminType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SetupController extends Controller
{
    /**
     * @Route("/setup", name="application_setup")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // set user repo, query users
        $userRepo = $this->getDoctrine()->getRepository('App:User');
        $users = $userRepo->findAll();

        // redirect to home page if users exist
        if ($users !== []){
            return $this->redirectToRoute('home');
        }

        // new user data
        $userData = new CreateAdminData();

        // build form
        $form = $this->createForm(CreateAdminType::class, $userData, array(
            'action' => $this->generateUrl('application_setup'),
        ));

        // process form
        $form->handleRequest($request);

        // check form
        if ($form->isSubmitted() && $form->isValid()){
            // create admin
            $admin = (new User())
                ->setFirstName($userData->firstName)
                ->setLastName($userData->lastName)
                ->setIsActive(true)
                ->setUsername($userData->username)
                ->setPassword($userData->password)
                ->setEmail($userData->email)
                ->setRoles(['ROLE_ADMIN']);

            // persist user, flush db
            $this->getDoctrine()->getManager()->persist($admin);
            $this->getDoctrine()->getManager()->flush();

            // forward to login
            return $this->redirectToRoute('login');
        }

        // render admin form / view
        return $this->render('setup/setup_index.html.twig', array(
            'form' => $form->createView()
        ));
    }
}