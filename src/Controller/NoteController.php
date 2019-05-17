<?php

namespace App\Controller;

use App\Entity\ActionLog;
use App\Entity\Note;
use App\Entity\User;
use App\Form\Note\NoteCreateType;
use App\Form\Note\NoteUpdateType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class NoteController extends AbstractController
{
    /**
     * @Route("/note/create/{entity}/{id}/{lock}", name="note_create")
     * @param Request $request
     * @param UserInterface|User $user
     * @param string $entity
     * @param string $id
     * @param bool $lock
     * @return Response
     */
    public function createAction(Request $request, UserInterface $user, string $entity, string $id, bool $lock = false): Response
    {
        // set repo, query entity
        $entityRepo = $this->getDoctrine()->getRepository(urldecode($entity));
        $noteRepo = $this->getDoctrine()->getRepository('App:Note');

        // make sure repo is correct
        if ($entityRepo === null){
            return new Response('Comment system error - incorrect repository.');
        }

        $workingEntity = $entityRepo->find($id);

        // make sure entity exists
        if ($workingEntity === null){
            return new Response('Comment system error - missing entity.');
        }

        // query existing notes
        /** @var Note[] $existingNotes */
        $existingNotes = $noteRepo->findBy(array(
            'entityName' => urldecode($entity),
            'entityId' => $id
        ), ['id' => 'DESC']);

        // create new note object
        $note = new Note();
        $note
            ->setEntityName(urldecode($entity))
            ->setEntityId($id)
            ->setUserCreated($user)
            ->setUserUpdated($user);

        // build form
        $form = $this->createForm(NoteCreateType::class, $note, array(
            'action' => $this->generateUrl('note_create', ['entity' => $entity, 'id' => $id]),
            'lock' => $lock
        ));

        // process form
        $form->handleRequest($request);

        // check form
        if ($form->isSubmitted() && $form->isValid()){

            // log action
            $log = new ActionLog();
            $log
                ->setAction('Comment Create')
                ->setDetail('Left for ' . urldecode($entity) . " with ID: $id")
                ->setUserCreated($user);

            // persist note, log, flush db
            $this->getDoctrine()->getManager()->persist($note);
            $this->getDoctrine()->getManager()->persist($log);
            $this->getDoctrine()->getManager()->flush();

            // redirect back
            return $this->redirectToRoute('note_create', ['entity' => $entity, 'id' => $id]);
        }

        return $this->render('note/note_view.html.twig', array(
            'note_form' => $form->createView(),
            'notes' => $existingNotes,
            'lock' => $lock
        ));
    }

    /**
     * @Route("/note/update/{id}", name="note_update")
     * @param Request $request
     * @param UserInterface|User $user
     * @param string $id
     * @return Response
     */
    public function updateAction(Request $request, UserInterface $user, string $id): Response
    {
        // set repo, query note
        $noteRepo = $this->getDoctrine()->getRepository('App:Note');
        /** @var Note $note */
        $note = $noteRepo->find($id);

        // make sure note exists
        if ($note === null){
            return new Response('Comment system error - missing.');
        }

        // make sure user created
        if ($note->getUserCreated() !== $user){
            return new Response('Comment system error - wrong user.');
        }

        // build form
        $form = $this->createForm(NoteUpdateType::class, $note, array(
            'action' => $this->generateUrl('note_update', ['id' => $id])
        ));

        // process form
        $form->handleRequest($request);

        // check form
        if ($form->isSubmitted() && $form->isValid()){

            // log action
            $log = new ActionLog();
            $log
                ->setAction('Comment Update')
                ->setDetail("Comment $id updated")
                ->setUserCreated($user);

            // persist log, flush db
            $this->getDoctrine()->getManager()->persist($log);
            $this->getDoctrine()->getManager()->flush();

            // return comment
            return new Response('<p id="note_text_' . $note->getId() . '">' . $note->getComment() . '</p>');
        }

        // return form
        return $this->render('note/note_update.html.twig', array(
            'note_update' => $form->createView()
        ));
    }

    /**
     * @Route("/note/delete/{id}", name="note_delete")
     * @param UserInterface|User $user
     * @param string $id
     * @return Response
     */
    public function deleteAction(UserInterface $user, string $id): Response
    {
        // set repo, query note
        $noteRepo = $this->getDoctrine()->getRepository('App:Note');
        /** @var Note $note */
        $note = $noteRepo->find($id);

        // make sure note exists
        if ($note === null){
            return new Response('Comment system error - missing.');
        }

        // make sure user created
        if ($note->getUserCreated() !== $user){
            return new Response('Comment system error - wrong user.');
        }

        // log action
        $log = new ActionLog();
        $log
            ->setAction('Comment Delete')
            ->setDetail("Comment $id removed")
            ->setUserCreated($user);

        // persist log, remove note, flush db
        $this->getDoctrine()->getManager()->persist($log);
        $this->getDoctrine()->getManager()->remove($note);
        $this->getDoctrine()->getManager()->flush();

        // return message to user
        return new Response('Comment deleted');
    }
}