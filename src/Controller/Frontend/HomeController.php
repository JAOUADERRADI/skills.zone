<?php

namespace App\Controller\Frontend;


use App\Entity\CourseCategory;
use App\Entity\Course;
use App\Entity\Post;
use App\Entity\PostCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $courses = $entityManager->getRepository(Course::class)->findAll();
        $posts = $entityManager->getRepository(Post::class)->findAll();

        shuffle($courses); shuffle($posts);
        $categories = $entityManager->getRepository(CourseCategory::class)->findAll();

        return $this->render('Frontend/home/index.html.twig', [
            'courses' => $courses,
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }
}
