<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /*#[Route('/contact', name: 'contact')]
    public function index(): Response
    {
        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }*/

    const SUCCESS_MESSAGE = [
        'title' => 'Köszönjük szépen a kérdésedet!',
        'message' => 'Válaszunkkal hamarosan keresünk a megadott e-mail címen.'
    ];
    
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request): Response
    {
        $form = $this->createForm(ContactFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Adatok feldolgozása, például e-mail küldés, adatbázis mentés, stb.
            // Példa: $data = $form->getData();
            // ... feldolgozás ...

            // Átirányítás vagy más válasz generálása
            return $this->redirectToRoute('success_page');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/success', name: 'success_page')]
    public function success(): Response
    {
        return $this->render('contact/success.html.twig',['message' => $this::SUCCESS_MESSAGE]);
    }

}
