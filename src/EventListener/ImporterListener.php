<?php

namespace App\EventListener;

use App\Entity\ActionLog;
use Doctrine\ORM\EntityManager;
use Musicjerm\Bundle\JermBundle\Events\ImporterImportEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ImporterListener
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
     * @param ImporterImportEvent $event
     * @throws \Exception
     */
    public function onImport(ImporterImportEvent $event)
    {
        $objectName = $event->getObject()['class'];
        $countNew = $event->getObject()['new'];
        $countUpdated = $event->getObject()['updated'];
        $user = $this->tokenStorage->getToken()->getUser();

        // set log details
        $detailString = "$objectName, ($countNew) New items.  ($countUpdated) updated items.";

        $log = new ActionLog();
        $log
            ->setAction('Import')
            ->setUserCreated($user)
            ->setDetail($detailString)
            ->setDateCreated(new \DateTime());

        $this->em->persist($log);
        $this->em->flush();
    }
}