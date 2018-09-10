<?php

namespace App\EventListener;

use App\Entity\ActionLog;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityListener
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var RequestStack $requestStack
     */
    private $requestStack;

    /**
     * @var AuthorizationChecker $authorizationChecker
     */
    private $authorizationChecker;

    public function __construct(EntityManager $entityManager, RequestStack $requestStack, AuthorizationChecker $authorizationChecker)
    {
        $this->em = $entityManager;
        $this->requestStack = $requestStack;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param AuthenticationFailureEvent $event
     * @throws \Exception
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        // executes on failed login

        /**
         * @var UserRepository $userRepo
         * @var User $user
         */
        $userRepo = $this->em->getRepository('App:User');
        $user = $userRepo->findOneBy(array(
            'username' => $event->getAuthenticationToken()->getUsername()
        ));

        if ($user === null){
            $user = $userRepo->findOneBy(array(
                'email' => $event->getAuthenticationToken()->getUsername()
            ));
        }

        $detail = 'Attempted from: '.$this->requestStack->getCurrentRequest()->getClientIp();
        $user !== null ?: $detail .= ', username: '.$event->getAuthenticationToken()->getUsername();

        $logObject = new ActionLog();
        $logObject->setAction('Failed Login');
        $logObject->setDetail($detail);
        $logObject->setUserCreated($user);
        $this->em->persist($logObject);
        $this->em->flush();
    }

    /**
     * @param InteractiveLoginEvent $event
     * @throws \Exception
     */
    public function onAuthenticationSuccess(InteractiveLoginEvent $event): void
    {
        // executes on successful login

        $detail = 'IP: '.$this->requestStack->getCurrentRequest()->getClientIp();
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')){
            $detail .= ', Authenticated Fully';
        }elseif($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            $detail .= ', Remembered';
        }

        $logObject = new ActionLog();
        $logObject->setAction('Successful Login');
        $logObject->setDetail($detail);
        $logObject->setUserCreated($event->getAuthenticationToken()->getUser());
        $this->em->persist($logObject);
        $this->em->flush();
    }
}