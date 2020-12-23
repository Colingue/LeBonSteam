<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Category;
use App\Form\SearchForm;
use App\Repository\PostRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
    public function index(PostRepository $postRepo, Request $request)
    {
        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $posts = $postRepo->findSearch($data);

        return $this->render('search/index.html.twig', [
            'posts' => $posts,
            'form' => $form->createView()
        ]);
    }

    public function searchBar() {
        $formSearch = $this->createFormBuilder(null)
            ->setAction($this->generateUrl('handle_search'))
            ->add('query', TextType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'required' => false,
                'empty_data' => 'Tous'
                ])
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
        $category = $request->request->getaDataDz4;
        if ($query) {
            $posts = $postRepo->findPostByName($query, $category);
        }

        return $this->render('lbs/index.html.twig', [
            'posts' => $posts
        ]);
    }
}
