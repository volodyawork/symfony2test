<?php
namespace VG\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use VG\GuestbookBundle\Entity\Message;
use VG\UserBundle\Entity\User;

class RegisterListener
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof User) {
            $em = $args->getEntityManager();
            // добавим сообщение в гостевую книгу.
            $message = new Message();
            $message->setName('admin');
            $message->setEmail('admin@my-super-site.com');
            $message->setText('Приветствуем нового пользователя ' . $entity->getName());
            $em->persist($message);
            $em->flush();
        }
    }
}




