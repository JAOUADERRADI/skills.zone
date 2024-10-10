<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\String\Slugger\SluggerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


#[Route('/admin/resource')]
#[IsGranted('ROLE_ADMIN')]
final class PostAdminController extends AbstractController
{
    #[Route(name: 'app_post_admin_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $postsQuery = $postRepository->findAll();
        $posts = $paginator->paginate(
            $postsQuery,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('Admin/post_admin/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'app_post_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security, SluggerInterface $slugger): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser(); // Get the currently authenticated user
            $post->setUser($user); // Set the user who is creating the post

            // File upload logic
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move($this->getParameter('images_directory'), $newFilename);
                $post->setImage($newFilename);
            }

            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_post_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/post_admin/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_admin_show', methods: ['GET'])]
    public function show(Post $post): Response
    {
        return $this->render('Admin/post_admin/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_post_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Post $post, EntityManagerInterface $entityManager, Security $security,): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser(); // Get the current user
            $post->updateTimestamp($user); // Update the timestamp and the user

            // Check if a new image is uploaded
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Delete the old image if exists
                $oldImage = $post->getImage();
                if ($oldImage) {
                    $oldImagePath = $this->getParameter('images_directory').'/'.$oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Handle the new image upload
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                $imageFile->move($this->getParameter('images_directory'), $newFilename);
                $post->setImage($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_post_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/post_admin/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_post_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->getPayload()->getString('_token'))) {
            // Get the image of the post
            $image = $post->getImage();
            if ($image) {
                // Delete the image file
                $imagePath = $this->getParameter('images_directory').'/'.$image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_post_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
