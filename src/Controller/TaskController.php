<?php

namespace App\Controller;

use App\Entity\ActionLog;
use App\Entity\Task;
use App\Entity\User;
use App\Form\CRUD\TaskType;
use App\Form\DTO\TaskData;
use App\Form\Transformer\TaskTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskController extends Controller
{
    /**
     * @Route("/task/create", name="create_task")
     * @param Request $request
     * @param UserInterface|User $user
     * @return Response
     */
    public function createAction(Request $request, UserInterface $user): Response
    {
        // create new task data
        $taskData = new TaskData();

        // build form
        $form = $this->createForm(TaskType::class, $taskData, array(
            'action' => $this->generateUrl('create_task')
        ));

        // process form
        $form->handleRequest($request);

        // check form
        if ($form->isSubmitted() && $form->isValid()){
            // entity manager
            $em = $this->getDoctrine()->getManager();

            // transform task data into task entity
            $taskTransformer = new TaskTransformer($user);
            $newTask = $taskTransformer->transform($taskData);

            // persist new task, flush db
            $em->persist($newTask);
            $em->flush();

            // check for attachment, save file
            if ($taskData->document !== null){
                $taskData->document->move(
                    $this->getParameter('kernel.project_dir') . '/uploads/Task/' . $newTask->getId() . '/',
                    $newTask->getDocument()
                );
            }

            // log action
            $log = new ActionLog();
            $log
                ->setAction('Create Task')
                ->setDetail($newTask->getId() . " - $taskData->title")
                ->setUserCreated($user);

            // persist log, flush db
            $em->persist($log);
            $em->flush();

            // return success notification to user
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => 'New task created!',
                'type' => 'success',
                'refresh' => true,
                'fade' => true
            ));
        }

        // return form to user
        return $this->render('@Jerm/Modal/form.html.twig', array(
            'header' => 'Create new Task',
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/task/view/{id}", name="view_task")
     * @param UserInterface|User $user
     * @param string $id
     * @return Response
     */
    public function viewAction(UserInterface $user, string $id): Response
    {
        // set repo and query task
        $taskRepo = $this->getDoctrine()->getRepository('App:Task');
        /** @var Task $task */
        $task = $taskRepo->find($id);

        // make sure task exists
        if ($task === null){
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => "Task $id does not exist.",
                'type' => 'error',
                'modal_size' => 'modal-sm',
                'refresh' => true
            ));
        }

        // make sure user is authorized
        if ($this->authViewCheck($user, $task) === false){
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => 'You do not have permission to view this task.',
                'type' => 'error'
            ));
        }

        // return view to user
        return $this->render('task/view_task.html.twig', array(
            'task' => $task
        ));
    }

    private function authViewCheck(User $user, Task $task): bool
    {
        // check if user created
        return ($task->getUserCreated() === $user);
    }
}