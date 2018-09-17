<?php

namespace App\Form\Transformer;

use App\Entity\Task;
use App\Form\DTO\TaskData;

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
        $task !== null ?: ($task = new Task())->setUserCreated($this->user);
        $task
            ->setTitle($taskData->title)
            ->setDescription($taskData->description)
            ->setFollowUp($taskData->followUp)
            ->setUserUpdated($this->user);

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

        // return task data
        return $taskData;
    }
}