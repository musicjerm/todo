<?php

namespace App\EventListener;

use App\Entity\ActionLog;
use Doctrine\ORM\EntityManager;
use Musicjerm\Bundle\JermBundle\Events\CrudCreateEvent;
use Musicjerm\Bundle\JermBundle\Events\CrudUpdateEvent;
use Musicjerm\Bundle\JermBundle\Events\CrudDeleteEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class CRUDListener
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var TokenStorage $tokenStorage
     */
    private $tokenStorage;

    public function __construct(EntityManager $entityManager, TokenStorage $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param CrudCreateEvent $event
     * @throws \Exception
     */
    public function onCrudCreate(CrudCreateEvent $event)
    {
        $workingObject = $event->getObject();
        $user = $this->tokenStorage->getToken()->getUser();

        // set user created/updated
        !method_exists($workingObject, 'setUserCreated') ?: $workingObject->setUserCreated($user);
        !method_exists($workingObject, 'setUserUpdated') ?: $workingObject->setUserUpdated($user);

        // set log detail string
        $reflect = new \ReflectionClass($workingObject);
        $detailString = $reflect->getShortName();
        !method_exists($workingObject, 'getId') ?: $detailString .= ' (' . $workingObject->getId() . ')';
        !method_exists($workingObject, '__toString') ?: $detailString .= ' - ' . $workingObject->__toString();

        $log = new ActionLog();
        $log
            ->setAction('Create')
            ->setUserCreated($user)
            ->setDetail($detailString)
            ->setDateCreated(new \DateTime());

        $this->em->persist($log);
        $this->em->flush();
    }

    /**
     * @param CrudUpdateEvent $event
     * @throws \Exception
     */
    public function onCrudUpdate(CrudUpdateEvent $event)
    {
        $workingObject = $event->getObject();
        $user = $this->tokenStorage->getToken()->getUser();

        // set user updated
        !method_exists($workingObject, 'setUserUpdated') ?: $workingObject->setUserUpdated($user);

        // set log detail string
        $reflect = new \ReflectionClass($workingObject);
        $detailString = $reflect->getShortName();
        !method_exists($workingObject, 'getId') ?: $detailString .= ' (' . $workingObject->getId() . ')';
        !method_exists($workingObject, '__toString') ?: $detailString .= ' - ' . $workingObject->__toString();

        $log = new ActionLog();
        $log
            ->setAction('Update')
            ->setUserCreated($user)
            ->setDetail($detailString)
            ->setDateCreated(new \DateTime());

        $this->em->persist($log);
        $this->em->flush();
    }

    /**
     * @param CrudDeleteEvent $event
     * @throws \Exception
     */
    public function onCrudDelete(CrudDeleteEvent $event)
    {
        $objectName = $event->getObject()['class'];
        $deleted = $event->getObject()['deleted'];
        $user = $this->tokenStorage->getToken()->getUser();

        // set log details
        $detailString = $objectName . ' [' . count($deleted) . ']';
        $detailString .= " items: \n" . implode("\n", $deleted);

        $log = new ActionLog();
        $log
            ->setAction('Delete')
            ->setUserCreated($user)
            ->setDetail($detailString)
            ->setDateCreated(new \DateTime());

        $this->em->persist($log);
        $this->em->flush();
    }
}