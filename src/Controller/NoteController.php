<?php

namespace App\Controller;

use App\Entity\ActionLog;
use App\Entity\Note;
use App\Entity\User;
use App\Form\Note\NoteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class NoteController extends Controller
{
    /**
     * @Route("/note/create/{entity}/{id}", name="note_create")
     * @param Request $request
     * @param UserInterface|User $user
     * @param string $entity
     * @param string $id
     * @return Response
     */
    public function createAction(Request $request, UserInterface $user, string $entity, string $id): Response
    {
        // set repo, query entity
        $entityRepo = $this->getDoctrine()->getRepository($entity);
        $noteRepo = $this->getDoctrine()->getRepository('App:Note');

        // make sure repo is correct
        if ($entityRepo === null){
            return new Response('Comment System Error');
        }

        $workingEntity = $entityRepo->find($id);

        // make sure entity exists
        if ($workingEntity === null){
            return new Response('Comment System Error');
        }

        // query existing notes
        /** @var Note[] $existingNotes */
        $existingNotes = $noteRepo->findBy(array(
            'entityName' => $entity,
            'entityId' => $id
        ), ['id' => 'DESC']);

        // create new note object
        $note = new Note();
        $note
            ->setEntityName($entity)
            ->setEntityId($id)
            ->setUserCreated($user)
            ->setUserUpdated($user);

        // build form
        $form = $this->createForm(NoteType::class, $note, array(
            'action' => $this->generateUrl('note_create', ['entity' => $entity, 'id' => $id])
        ));

        // process form
        $form->handleRequest($request);

        // check form
        if ($form->isSubmitted() && $form->isValid()){

            // log action
            $log = new ActionLog();
            $log
                ->setAction('Comment')
                ->setDetail("Left for $entity with ID: $id")
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
            'notes' => $existingNotes
        ));
    }
}