<?php

namespace App\Form\Task;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;

class TaskTransformer
{
    /** @var \App\Entity\User */
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function transform(TaskData $taskData, Task $task = null): Task
    {
        // create new task if none passed, set values
        if ($task === null){
            $task = new Task();
            $task
                ->setStatus('New')
                ->setUserCreated($this->user);
        }

        // set new values
        $taskData->title === null ?: $task->setTitle($taskData->title);
        $taskData->description === null ?: $task->setDescription($taskData->description);
        $taskData->followUp === null ?: $task->setFollowUp($taskData->followUp);
        $taskData->public === null ?: $task->setPublic($taskData->public);
        $taskData->priority === null ?: $task->setPriority($taskData->priority);
        $taskData->status === null ?: $task->setStatus($taskData->status);
        $taskData->targetCompleteDate === null ?: $task->setTargetCompleteDate($taskData->targetCompleteDate);

        // if user groups selected, subscribe users to task
        if ($taskData->userGroups !== null){
            /** @var Collection|User[] $userArray */
            $userArray = array($this->user);
            foreach ($taskData->userGroups as $userGroup){
                /** @var \App\Entity\User $user */
                foreach ($userGroup->getUsers() as $user){
                    \in_array($user, $userArray, true) ?: $userArray[] = $user;
                }
            }
            $task->setUserSubscribed($userArray);
        }

        // if tag string set, create and set array
        if ($taskData->tags !== null){
            $tagArray = array();
            foreach (\explode(',', $taskData->tags) as $tag){
                $tagArray[] = trim($tag);
            }
            $task->setTags($tagArray);
        }

        // set user updated
        $task->setUserUpdated($this->user);

        // set file name if document uploaded
        if ($taskData->document !== null){
            $task->setDocument($taskData->document->getClientOriginalName());
        }

        // return task
        return $task;
    }

    public function reverseTransform(Task $task): TaskData
    {
        // create new task data from queried object
        $taskData = new TaskData();
        $taskData->title = $task->getTitle();
        $taskData->description = $task->getDescription();
        $taskData->followUp = $task->getFollowUp();
        $taskData->public = $task->getPublic();
        $taskData->priority = $task->getPriority();
        $taskData->status = $task->getStatus();
        $taskData->tags = implode(', ', $task->getTags());

        // return task data
        return $taskData;
    }
}