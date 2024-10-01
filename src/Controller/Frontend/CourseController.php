<?php

namespace App\Controller\Frontend;

use App\Entity\Course;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


class CourseController extends AbstractController
{
    #[Route('/course', name: 'app_course')]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $coursesQuery = $entityManager->getRepository(Course::class)->createQueryBuilder('c')->getQuery();

        $courses = $paginator->paginate(
            $coursesQuery,
            $request->query->getInt('page', 1),
            9
        );
        return $this->render('Frontend/course/index.html.twig', [
            'courses' => $courses,
        ]);
    }
}
