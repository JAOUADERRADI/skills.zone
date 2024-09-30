<?php

namespace App\Controller\Admin;


use App\Repository\UserRepository;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(UserRepository $userRepository, CourseRepository $courseRepository, LessonRepository $lessonRepository): Response
    {
        $totalUsers = $userRepository->count([]);

        $totalCourses = $courseRepository->count([]);

        $totalLessons = $lessonRepository->count([]);

        // $newUsersThisMonth = $userRepository->countNewUsersThisMonth();
        return $this->render('Admin/index.html.twig', [
            'totalUsers' => $totalUsers,
            'totalCourses' => $totalCourses,
            'totalLessons' => $totalLessons,
            // 'newUsersThisMonth' => $newUsersThisMonth,
        ]);
    }
}
