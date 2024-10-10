<?php

namespace App\Controller\Frontend;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class PostController extends AbstractController
{
    #[Route('/resources', name: 'app_post')]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $postsQuery = $entityManager->getRepository(Post::class)->createQueryBuilder('p')->getQuery();
        $posts = $paginator->paginate(
            $postsQuery,
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('Frontend/post/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
