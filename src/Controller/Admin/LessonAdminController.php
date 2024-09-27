<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Entity\User;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('/admin/lesson')]
#[IsGranted('ROLE_ADMIN')]
final class LessonAdminController extends AbstractController
{
    #[Route(name: 'app_lesson_admin_index', methods: ['GET'])]
    public function index(LessonRepository $lessonRepository): Response
    {
        return $this->render('Admin/lesson_admin/index.html.twig', [
            'lessons' => $lessonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_lesson_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('video')->getData();

            if ($videoFile) {
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

                $videoFile->move(
                    $this->getParameter('videos_directory'),
                    $newFilename
                );

                $lesson->setVideo($newFilename);
            }
            $entityManager->persist($lesson);
            $entityManager->flush();

            return $this->redirectToRoute('app_lesson_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/lesson_admin/new.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lesson_admin_show', methods: ['GET'])]
    public function show(Lesson $lesson): Response
    {
        return $this->render('Admin/lesson_admin/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_lesson_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lesson $lesson, EntityManagerInterface $entityManager, Security $security): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('video')->getData();

            if ($videoFile) {

                // Delete the old video if it exists
                $oldVideo = $lesson->getVideo();
                if ($oldVideo) {
                    $oldVideoPath = $this->getParameter('videos_directory').'/'.$oldVideo;
                    if (file_exists($oldVideoPath)) {
                        unlink($oldVideoPath);
                    }
                }

                // Handle the new video upload
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

                $videoFile->move($this->getParameter('videos_directory'), $newFilename);
                $lesson->setVideo($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_lesson_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/lesson_admin/edit.html.twig', [
            'lesson' => $lesson,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_lesson_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $request->getPayload()->getString('_token'))) {
            // Delete the video associated with the lesson
            $video = $lesson->getVideo();
            if ($video) {
                $videoPath = $this->getParameter('videos_directory').'/'.$video;
                if (file_exists($videoPath)) {
                    unlink($videoPath);
                }
            }

            // Delete the lesson
            $entityManager->remove($lesson);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lesson_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
