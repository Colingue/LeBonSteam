<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{

    /**
     * @Route("/registration", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/login", name="security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils){

        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('security/login.html.twig', [
            'error' => $error
        ]);
    }

    /**
     * @Route ("/logout", name="security_logout")
     */
    public function logout() {}

    /**
     * @Route ("/profile/{id}", name="profile")
     */
   public function profile(User $user ,Request $request) {
        return $this->render('profile/view_profile.html.twig');
   }

    /**
     * @Route ("/profile/{id}/edit", name="edit_profile")
     */
   public function editProfil(User $user, Request $request, EntityManagerInterface $manager)
   {
        $form = $this->createFormBuilder($user)
            ->add('username')
            ->add('save', SubmitType::class)
            ->getForm();

        $username = $user->getUsername();
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()){

           $manager->persist($user);
           $manager->flush();

           return $this->redirectToRoute('profile', array('id' => $user->getId()));
       }
       else{
           $user->setUsername($username);
       }


       return $this->render('profile/edit_profile.html.twig', [
           'formEditUser' => $form->createView()
       ]);
   }
}


