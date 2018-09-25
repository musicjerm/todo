<?php

namespace App\Controller;

use App\Entity\ActionLog;
use App\Entity\Task;
use App\Entity\User;
use App\Form\Task\TaskData;
use App\Form\Task\TaskCreateType;
use App\Form\Task\TaskTransformer;
use App\Form\Task\TaskUpdateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
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
        $form = $this->createForm(TaskCreateType::class, $taskData, array(
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
     * @Route("/task/update/{id}", name="update_task")
     * @param UserInterface|User $user
     * @param Request $request
     * @param string $id
     * @return Response
     */
    public function updateAction(UserInterface $user, Request $request, string $id): Response
    {
        // set repo, query task
        $taskRepo = $this->getDoctrine()->getRepository('App:Task');
        /** @var Task $task */
        $task = $taskRepo->find($id);

        // make sure task exists
        if ($task === null){
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => "Task #$id does not exist.",
                'type' => 'error',
                'modal_size' => 'modal-sm'
            ));
        }

        // make sure user has edit rights
        if ($this->authEditCheck($user, $task) === false){
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => "You are not authorized to make changes to task #$id.  Please leave a comment instead.",
                'type' => 'error'
            ));
        }

        // create task data from entity
        $taskTransformer = new TaskTransformer($user);
        $taskData = $taskTransformer->reverseTransform($task);

        // build form
        $form = $this->createForm(TaskUpdateType::class, $taskData, array(
            'action' => $this->generateUrl('update_task', ['id' => $id])
        ));

        // process form
        $form->handleRequest($request);

        // check form
        if ($form->isSubmitted() && $form->isValid()){
            // entity manager
            $em = $this->getDoctrine()->getManager();

            // update task entity
            $taskTransformer->transform($taskData, $task);

            // check for attachment, save file
            if ($taskData->document !== null){
                // remove old file if exists
                $savePath = $this->getParameter('kernel.project_dir') . '/uploads/Task/' . $task->getId() . '/';
                $fs = new Filesystem();
                $fs->remove($savePath);

                // save new file
                $taskData->document->move($savePath, $task->getDocument());
            }

            // log action
            $log = new ActionLog();
            $log
                ->setAction('Update Task')
                ->setDetail($task->getId() . ' - ' . $task->getTitle())
                ->setUserCreated($user);

            // persist log, flush db
            $em->persist($log);
            $em->flush();

            // return user to view
            return $this->redirectToRoute('view_task', ['id' => $id]);
        }

        // return form to user
        return $this->render('task/update_task.html.twig', array(
            'header' => "Update Task #$id",
            'form' => $form->createView(),
            'previous_path' => $this->generateUrl('view_task', ['id' => $id])
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

        // build view array
        $viewArray = array(
            'task' => $task,
            'editable' => $this->authEditCheck($user, $task),
            'target_diff_string' => null,
            'target_diff_class' => null,
            'target_diff_icon' => null,
            'status_class' => array(
                'New' => 'success',
                'Open' => 'primary',
                'Work in Progress' => 'info',
                'Closed' => 'default',
                'On Hold' => 'default',
                'Cancelled' => 'danger'
            )
        );

        // set target complete date view properties if not null
        if ($task->getTargetCompleteDate() !== null && \in_array($task->getStatus(), ['Open', 'Work in Progress', 'New'])){
            $datePropertyGen = new \App\Utilities\DatePropertyGenerator($task->getDaysToTargetCompleteDate());

            $viewArray['target_diff_string'] = $datePropertyGen->string;
            $viewArray['target_diff_class'] = $datePropertyGen->color;
            $viewArray['target_diff_icon'] = $datePropertyGen->icon;
        }

        // return view to user
        return $this->render('task/view_task.html.twig', $viewArray);
    }

    /**
     * @Route("/task/delete_doc/{id}", name="task_delete_doc")
     * @param UserInterface|User $user
     * @param string $id
     * @return Response
     */
    public function removeDocument(UserInterface $user, string $id): Response
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
        if ($this->authEditCheck($user, $task) === false){
            return $this->render('@Jerm/Modal/notification.html.twig', array(
                'message' => 'You do not have permission to perform this task.',
                'type' => 'error'
            ));
        }

        // remove document/files
        $savePath = $this->getParameter('kernel.project_dir') . '/uploads/Task/' . $task->getId() . '/';
        $fs = new Filesystem();
        $fs->remove($savePath);

        // log action
        $log = new ActionLog();
        $log
            ->setAction('Delete Task Document')
            ->setDetail($task->getDocument() . ' removed from Task #'. $task->getId())
            ->setUserCreated($user);

        // set document null, persist log, flush db
        $task->setDocument(null);
        $this->getDoctrine()->getManager()->persist($log);
        $this->getDoctrine()->getManager()->flush();

        // return user to view
        return $this->redirectToRoute('view_task', ['id' => $id]);
    }

    private function authViewCheck(User $user, Task $task): bool
    {
        // check if user is admin
        if ($this->isGranted('ROLE_ADMIN')){
            return true;
        }

        // check if user created
        if ($task->getUserCreated() === $user){
            return true;
        }

        // check if task is public
        if ($task->getPublic() === true){
            return true;
        }

        // check if user is subscribed
        if ($task->getUserSubscribed()->contains($user)){
            return true;
        }

        // fail auth check if none of the above conditions is met
        return false;
    }

    private function authEditCheck(User $user, Task $task): bool
    {
        // check if user is admin
        if ($this->isGranted('ROLE_ADMIN')){
            return true;
        }

        // check if user created
        if ($task->getUserCreated() === $user){
            return true;
        }

        // fail auth check if none of the above conditions is met
        return false;
    }
}