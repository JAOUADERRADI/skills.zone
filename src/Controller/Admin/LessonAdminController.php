<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/lesson')]
final class LessonAdminController extends AbstractController
{
    #[Route(name: 'app_lesson_admin_index', methods: ['GET'])]
    public function index(LessonRepository $lessonRepository): Response
    {
        return $this->render('lesson_admin/index.html.twig', [
            'lessons' => $lessonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_lesson_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lesson);
            $entityManager->flush();

            return $this->redirectToRoute('app_lesson_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lesson_admin/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lesson_admin_show', methods: ['GET'])]
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson_admin/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lesson_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_lesson_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lesson_admin/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lesson_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($lesson);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lesson_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
