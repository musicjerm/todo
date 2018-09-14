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

    public function transform(TaskData $taskData): Task
    {
        // create new task
        $task = new Task();
        $task
            ->setName($taskData->name)
            ->setDescription($taskData->description)
            ->setUserCreated($this->user)
            ->setUserUpdated($this->user);

        if ($taskData->document !== null){
            $task->setDocument($taskData->document->getClientOriginalName());
        }

        return $task;
    }

    public function reverseTransform(Task $task): TaskData
    {
        // create new task data
        $taskData = new TaskData();
        $taskData->name = $task->getName();
        $taskData->description = $task->getDescription();

        return $taskData;
    }
}