<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LbsController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('lbs/index.html.twig');
    }

    /**
     * @Route("/new", name="new_post")
     */
    public function createPost(Request $request, EntityManagerInterface $manager)
    {

        $post = new Post();

        $formPostCreator = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('categories', EntityType::class, array(
                'class' => Category::class,
                'multiple' => true))
            ->add('download_link')
            ->getForm();

        $formPostCreator->handleRequest($request);

        if ($formPostCreator->isSubmitted() && $formPostCreator->isValid())
        {
            $post->setUser($this->getUser());
            $post->setDateCreation(new \DateTime());
            $post->setDownloads(0);

            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('post/create_post.html.twig', [
            'formPostCreator' => $formPostCreator->createView()
        ]);
    }
}
