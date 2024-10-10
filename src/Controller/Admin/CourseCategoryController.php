<?php

namespace App\Controller\Admin;

use App\Entity\CourseCategory;
use App\Form\CourseCategoryType;
use App\Repository\CourseCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


#[Route('admin/course-category')]
#[IsGranted('ROLE_ADMIN')]
final class CourseCategoryController extends AbstractController
{
    #[Route(name: 'app_course_category_index', methods: ['GET'])]
    public function index(CourseCategoryRepository $courseCategoryRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $courseCategoryRepository->createQueryBuilder('cc')->getQuery();
        $coursesCategory = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('Admin/course_category/index.html.twig', [
            'course_categories' => $coursesCategory,
        ]);
    }

    #[Route('/new', name: 'app_course_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $courseCategory = new CourseCategory();
        $form = $this->createForm(CourseCategoryType::class, $courseCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($courseCategory);
            $entityManager->flush();

            return $this->redirectToRoute('app_course_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/course_category/new.html.twig', [
            'course_category' => $courseCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_course_category_show', methods: ['GET'])]
    public function show(CourseCategory $courseCategory): Response
    {
        return $this->render('Admin/course_category/show.html.twig', [
            'course_category' => $courseCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_course_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CourseCategory $courseCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CourseCategoryType::class, $courseCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_course_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/course_category/edit.html.twig', [
            'course_category' => $courseCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_course_category_delete', methods: ['POST'])]
    public function delete(Request $request, CourseCategory $courseCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$courseCategory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($courseCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_course_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
