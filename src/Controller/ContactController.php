<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Ticket;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ContactController extends AbstractController
{
    const SUCCESS_MESSAGE = 'Köszönjük szépen a kérdésedet! Válaszunkkal hamarosan keresünk a megadott e-mail címen.';
    const ERROR_MESSAGE = 'Hiba! Kérjük töltsd ki az összes mezőt!';
    
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $ticket = new Ticket();
        $form = $this->createForm(ContactFormType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ticket);
            $entityManager->flush();
            $session->getFlashBag()->add('success', $this::SUCCESS_MESSAGE);
            
            return $this->redirectToRoute('contact');
        } 

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
