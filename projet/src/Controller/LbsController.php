<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\PostDownload;
use App\Entity\User;
use App\Form\SearchForm;
use App\Form\SearchType;
use App\Repository\CategoryRepository;
use App\Repository\PostDownloadRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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

    // Page d'accueil avec tous les posts
    public function index(PostRepository $postRepo, Request $request): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $posts = $postRepo->findSearch($data);

        return $this->render('lbs/index.html.twig', [
            'posts' => $posts ,
            'form' => $form->createView()
        ]);
    }

    // Page d'un post en particulier
    /**
     * @Route ("/post/{id}", name="show_post")
     */
    public function showPost(Post $post)
    {
        $repo = $this->getDoctrine()->getRepository(Post::class);

        $post = $repo->find($post->getId());

        return $this->render('post/show_post.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route ("/post/{id}/edit", name="edit_post")
     * @Route("/new", name="new_post")
     */

    // Page pour créer un post
    public function createPost(Post $post = null, Request $request, EntityManagerInterface $manager)
    {
        if (!$post){
            $post = new Post();
        }

        $formPostCreator = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('category', EntityType::class, array(
                'class' => Category::class))
            ->add('download_link')
            ->add('imageFile', FileType::class, [
                'required' => false
            ])
            ->getForm();

        $formPostCreator->handleRequest($request);

        if ($formPostCreator->isSubmitted() && $formPostCreator->isValid())
        {
            $post->setUser($this->getUser());
            $post->setDateCreation(new \DateTime());

            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('post/create_post.html.twig', [
            'formPostCreator' => $formPostCreator->createView()
        ]);
    }

    /**
     * Permet de download un contenu (lien)
     *
     * @Route ("/post/{id}/download", name="post_download")
     *
     * @param Post $post
     * @param ObjectManager $manager
     * @param PostDownloadRepository $repo
     * @return Response
     */
    public function download(Post $post, EntityManagerInterface $manager, PostDownloadRepository $downloadRepository) : Response {
        $user = $this->getUser();

        if (!$user){
            return $this->json([
                'code' => '401',
                'message' => 'Vous devez être connecté.'
            ], 401);
        }

        elseif ($post->isDownloadedByUser($user)){
            return $this->json([
                'code' => '200',
                'message' => 'Post déjà download',
                'download' => $downloadRepository->count(['post' => $post]),
                'link' => $post->getDownloadLink()
            ], 200);
        }



        $download = new PostDownload();
        $download->setUser($user);
        $download->setPost($post);

        $manager->persist($download);
        $manager->flush();


        return $this->json([
            'code' => 200,
            'message' => 'Download bien ajouté',
            'download' => $downloadRepository->count(['post' => $post]),
            'link' => $post->getDownloadLink()],
            200);
    }



    public function search(Request $request, PostRepository $postRepository)
    {


        $formSearch = $this->createForm(SearchType::class);
        $search = $formSearch->handleRequest($request);

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $posts = $postRepository->search($search->get('word')->getData());
        }

        return $this->render('search/search.html.twig', [
            'posts' => $posts,
            'formSearch' => $formSearch->createView()
        ]);
    }
}
