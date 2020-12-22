<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function index(): Response
    {
        return $this->render('search/index.html.twig', [
            'controller_name' => 'SearchController',
        ]);
    }

    public function searchBar() {
        $formSearch = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('handle_search'))
            ->add('query', TextType::class)
            ->add('search', SubmitType::class)
            ->getForm();

        return $this->render('search/search_bar.html.twig', [
            'formSearch' => $formSearch->createView()
        ]);
    }


    /**
     * @Route("/Handlesearch", name="handle_search")
     * @param Request $request
     */
    public function handleSearch(Request $request, PostRepository $postRepo) {
        $query = $request->request->get('form')['query'];
        if ($query) {
            $posts = $postRepo->findPostByName($query);
        }

        return $this->render('lbs/index.html.twig', [
            'posts' => $posts
        ]);
    }
}