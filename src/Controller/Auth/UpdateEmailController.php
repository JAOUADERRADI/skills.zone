<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\UpdateEmailType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\SecurityBundle\Security;

class UpdateEmailController extends AbstractController
{
    #[Route('profile/update-email', name: 'app_user_update_email', methods: ['GET', 'POST'])]
    public function updateEmail(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UpdateEmailType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newEmail = $form->get('email')->getData();

            $user->setEmail($newEmail);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Your email has been updated successfully!');
        }

        return $this->render('Frontend/info_user/update_email.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
