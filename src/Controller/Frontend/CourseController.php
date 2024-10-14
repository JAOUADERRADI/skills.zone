<?php

namespace App\Controller\Frontend;

use App\Entity\Course;
use App\Entity\Enrollment;
use App\Repository\CourseRepository;
use App\Repository\EnrollmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\SearchCourseType;
use App\Entity\CourseCategory;


class CourseController extends AbstractController
{
    #[Route('/course', name: 'app_course')]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        // Create the form for searching courses
        $form = $this->createForm(SearchCourseType::class);
        $form->handleRequest($request);

        // Initialize the query builder to retrieve all courses
        $queryBuilder = $entityManager->getRepository(Course::class)->createQueryBuilder('c');

        // If the form is submitted and valid, apply the filters
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData(); // Get the form data

            // Filter by keyword
            if ($data['keyword']) {
                $queryBuilder->andWhere('c.title LIKE :keyword')
                                ->setParameter('keyword', '%' . $data['keyword'] . '%');
            }

            // Filter by category if selected
            if ($data['category']) {
                $queryBuilder->andWhere('c.category = :category')
                    ->setParameter('category', $data['category']);
            }
        }

        // Get the query from the query builder
        $coursesQuery = $queryBuilder->getQuery();

        // Paginate the results (9 courses per page)
        $courses = $paginator->paginate(
            $coursesQuery,
            $request->query->getInt('page', 1),
            9
        );


        // Render the template and pass the courses and the search form to the view
        return $this->render('Frontend/course/index.html.twig', [
            'courses' => $courses,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/course/{id}', name: 'app_course_show', methods: ['GET'])]
    public function show(Course $course, EnrollmentRepository $enrollmentRepository): Response
    {
        $user = $this->getUser();
        $isEnrolled = false;

        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $isEnrolled = $enrollmentRepository->findOneBy([
                'user' => $user,
                'course' => $course,
            ]) !== null;
        }

        return $this->render('Frontend/course/show.html.twig', [
            'course' => $course,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    #[Route('/course/enroll/{id}', name: 'app_course_enroll', methods: ['POST'])]
    public function enroll(Course $course, EntityManagerInterface $entityManager, Request $request): RedirectResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $existingEnrollment = $entityManager->getRepository(Enrollment::class)->findOneBy([
            'user' => $user,
            'course' => $course,
        ]);

        if (!$existingEnrollment) {
            $enrollment = new Enrollment();
            $enrollment->setUser($user);
            $enrollment->setCourse($course);

            $entityManager->persist($enrollment);
            $entityManager->flush();

            $this->addFlash('success', 'You have successfully enrolled in the course!');
        } else {
            $this->addFlash('warning', 'You are already enrolled in this course.');
        }

        return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
    }

    #[Route('/my-courses', name: 'user_courses')]
    public function myCourses(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $enrollments = $entityManager->getRepository(Enrollment::class)->findBy([
            'user' => $user
        ]);

        return $this->render('Frontend/course/my_courses.html.twig', [
            'enrollments' => $enrollments,
        ]);
    }
}
