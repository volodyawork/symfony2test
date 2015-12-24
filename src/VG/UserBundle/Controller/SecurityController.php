<?php

namespace VG\UserBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\SecurityContextInterface;
use VG\GuestbookBundle\Entity\Message;
use VG\UserBundle\Entity\User;


class SecurityController extends Controller{

    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContextInterface::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContextInterface::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
            $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'VGUserBundle:Security:login.html.twig',
            [
                'last_username' => $session->get(SecurityContextInterface::LAST_USERNAME),
                'error'         => $error
            ]
        );
    }

    public function registrationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $error_message="";
        $roles = ['ROLE_ADMIN'];
        $user = new User($roles);
        $form = $this->createFormBuilder($user)
            ->add('name', 'text')
            ->add('password', 'password')
            ->add('email', 'email')
            ->add('register', 'submit')
            ->getForm();
        $form->handleRequest($request);

        if($form->isValid()){
            $user->setEmail($form['email']->getData());
            $user->setSalt(md5(time()));
            $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
            $password = $encoder->encodePassword($form['password']->getData(), $user->getSalt());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('login_path'));
        }

        return $this->render(
            'VGUserBundle:Security:registration.html.twig',['form'=>$form->createView(),
                'error_message'=>$error_message]

        );
    }

    public function securityCheckAction()
    {
        // The security layer will intercept this request
    }

    public function logoutAction()
    {
        // The security layer will intercept this request
    }
}