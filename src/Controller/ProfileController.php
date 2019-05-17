<?php

namespace App\Controller;

use App\Entity\ActionLog;
use App\Entity\User;
use App\Form\User\ProfileUpdateType;
use App\Form\User\ResetPasswordData;
use App\Form\User\ResetPasswordType;
use App\Form\User\UserUpdateData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('profile/profile_index.html.twig');
    }

    /**
     * @Route("/profile/update", name="profile_update")
     * @param Request $request
     * @param UserInterface|User $user
     * @return Response
     */
    public function update(Request $request, UserInterface $user): Response
    {
        // set user data
        $userData = new UserUpdateData();
        $userData->setDataFromObject($user);

        // build form
        $form = $this->createForm(ProfileUpdateType::class, $userData, array(
            'action' => $this->generateUrl('profile_update')
        ));

        // process form
        $form->handleRequest($request);

        // check form
        if ($form->isSubmitted() && $form->isValid()){
            // update user entity
            $user->setDataFromDTO($userData);

            // save profile pic
            if ($userData->profilePic !== null){
                $filePath = $this->getParameter('kernel.project_dir') . '/public/app/img/userProfile';
                $fileName = $user->getId() . '.' . $userData->profilePic->guessExtension();
                $userData->profilePic->move($filePath, $fileName);
            }

            // log action
            $log = (new ActionLog())
                ->setAction('Profile Update')
                ->setDetail('User updated their profile info.')
                ->setUserCreated($user);

            // persist log, flush db
            $this->getDoctrine()->getManager()->persist($log);
            $this->getDoctrine()->getManager()->flush();

            // return notification to user, refresh page
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => 'Profile updated',
                'modal_size' => 'modal-sm',
                'full_refresh' => true,
                'fade' => true
            ));
        }

        // return form to user
        return $this->render('@Jerm/Modal/form.html.twig', array(
            'header' => 'Update profile info',
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/profile/password_update", name="profile_password_update")
     * @param Request $request
     * @param UserInterface|User $user
     * @return Response
     */
    public function passwordUpdate(Request $request, UserInterface $user): Response
    {
        // set password reset data
        $resetPasswordDTO = new ResetPasswordData();

        // build form
        $form = $this->createForm(ResetPasswordType::class, $resetPasswordDTO, array(
            'action' => $this->generateUrl('profile_password_update')
        ));

        // process form
        $form->handleRequest($request);

        // check form
        if ($form->isSubmitted() && $form->isValid()){
            // entity manager
            $em = $this->getDoctrine()->getManager();

            // set user password
            $user->setPassword($resetPasswordDTO->password);

            // log action
            $log = new ActionLog();
            $log
                ->setAction('Password Change')
                ->setDetail('User updated')
                ->setUserCreated($user);

            // persist log, flush db
            $em->persist($log);
            $em->flush();

            // return success notification to user
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => 'Password updated successfully!',
                'modal_size' => 'modal-sm',
                'type' => 'success',
                'fade' => true
            ));
        }

        return $this->render('profile/change_password.html.twig', array(
            'header' => 'Change Password',
            'form' => $form->createView()
        ));
    }
}