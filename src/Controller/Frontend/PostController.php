<?php

namespace App\Controller\Frontend;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\SearchResourceType;
use App\Entity\PostCategory;

class PostController extends AbstractController
{
    #[Route('/resources', name: 'app_post')]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        // Create the form for searching posts
        $form = $this->createForm(SearchResourceType::class);
        $form->handleRequest($request);

        // Initialize the query builder to retrieve all posts
        $queryBuilder = $entityManager->getRepository(Post::class)->createQueryBuilder('p');

        // If the form is submitted and valid, apply the filters
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); // Get the form data

            // Filter by keyword
            if ($data['keyword']) {
                $queryBuilder->andWhere('p.title LIKE :keyword')
                    ->setParameter('keyword', '%' . $data['keyword'] . '%');
            }

            // Filter by category if selected
            if ($data['category']) {
                $queryBuilder->andWhere('p.category = :category')
                    ->setParameter('category', $data['category']);
            }
        }

        // Get the query from the query builder
        $postsQuery = $queryBuilder->getQuery();

        // Paginate the results (9 posts per page)
        $posts = $paginator->paginate(
            $postsQuery,
            $request->query->getInt('page', 1),
            9
        );

        // Render the template and pass the posts and the search form to the view
        return $this->render('Frontend/post/index.html.twig', [
            'posts' => $posts,
            'form' => $form->createView(),
        ]);
    }
}
