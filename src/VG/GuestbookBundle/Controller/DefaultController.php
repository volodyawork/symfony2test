<?php

namespace VG\GuestbookBundle\Controller;

use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use VG\GuestbookBundle\Entity\Message;

class DefaultController extends Controller
{
    /** Вывод всех сообщений гостевой книги в хронологическом порядке + формы добавления сообщения
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $message = new Message();
        //$message->setText('');
        $form = $this->createFormBuilder($message)
            ->add('name', 'text')
            ->add('email', 'email')
            ->add('text', 'textarea')
            ->add('save', 'submit', array('label' => 'Отправить'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();
            return $this->redirect('/');
        }

        $em = $this->getDoctrine()->getManager();

        $criteria = Criteria::create()
            ->orderBy(["id" => Criteria::DESC]);

        $entities = $em->getRepository('VGGuestbookBundle:Message')->matching($criteria);
        //$entities = $em->getRepository('VGGuestbookBundle:Message')->findAll();

        return $this->render('VGGuestbookBundle:Default:index.html.twig', [
            'form' => $form->createView(),
            'entities' => $entities,
        ]);
    }
}
