<?php

namespace App\Controller;

use App\Entity\ActionLog;
use App\Entity\User;
use App\Form\User\UserType;
use App\Form\User\UserData;
use App\Form\User\UserUpdateData;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends Controller
{
    /**
     * @Route("/admin/user/create", name="user_create")
     * @param Request $request
     * @param UserInterface|User $user
     * @return Response
     */
    public function createAction(Request $request, UserInterface $user): Response
    {
        // new user data
        $userData = new UserData();

        // build form
        $form = $this->createForm(UserType::class, $userData, array(
            'action' => $this->generateUrl('user_create'),
        ));

        // process form
        $form->handleRequest($request);

        // check form
        if ($form->isSubmitted() && $form->isValid()){
            // entity manager
            $em = $this->getDoctrine()->getManager();

            // create new user entity
            $newUser = new User();
            $newUser->setDataFromDTO($userData);

            // set non-dto properties
            $newUser
                ->setUserCreated($user)
                ->setUserUpdated($user);

            // persist user, flush db
            $em->persist($newUser);
            $em->flush();

            // log action
            $logMessage = 'User ' . $newUser->getId() . ' <' . $newUser->getEmail() . '> created.';
            $log = new ActionLog();
            $log
                ->setAction('Create User')
                ->setDetail($logMessage)
                ->setUserCreated($user);

            // persist log, flush db
            $em->persist($log);
            $em->flush();

            // perform other actions, ie: email user here

            // display notification to admin
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => $logMessage,
                'type' => 'success',
                'refresh' => true,
                'fade' => true
            ));
        }

        // return form to admin
        return $this->render('@Jerm/Modal/form.html.twig', array(
            'header' => 'Create new user',
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/user/update/{id}", name="user_update")
     * @param Request $request
     * @param UserInterface|User $user
     * @param string $id
     * @return Response
     */
    public function updateAction(Request $request, UserInterface $user, string $id): Response
    {
        // set user repo, query user by ID
        $userRepo = $this->getDoctrine()->getRepository('App:User');
        /** @var User $workingUser */
        $workingUser = $userRepo->find($id);

        // make sure user exists
        if ($workingUser === null){
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => "User with ID: $id could not be found.",
                'type' => 'error'
            ));
        }

        // create base DTO from existing user entity
        $userData = new UserUpdateData();
        $userData->setDataFromObject($workingUser);

        // build form
        $form = $this->createForm(UserType::class, $userData, ['action' => $this->generateUrl('user_update', ['id' => $id])]);

        // process form
        $form->handleRequest($request);

        // check form
        if ($form->isSubmitted() && $form->isValid()){
            // entity manager
            $em = $this->getDoctrine()->getManager();

            // update user entity
            $workingUser->setDataFromDTO($userData);

            // set non-dto properties
            $workingUser->setUserUpdated($user);

            // log action
            $logMessage = "User $id updated successfully.";
            $log = new ActionLog();
            $log
                ->setAction('Update User')
                ->setDetail($logMessage)
                ->setUserCreated($user);

            // persist log, flush db
            $em->persist($log);
            $em->flush();

            // return success notification to admin
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => $logMessage,
                'type' => 'success',
                'refresh' => true,
                'fade' => true
            ));
        }

        // return form to admin
        return $this->render('@Jerm/Modal/form.html.twig', array(
            'header' => "Updated user $id",
            'form' => $form->createView()
        ));
    }
}