<?php

namespace App\Controller;

use App\DTO\AdminFormDTO;
use App\DTO\TicketFormDTO;
use App\Entity\Admin;
use App\Entity\Ticket;
use App\Form\AdminFormType;
use App\Form\TicketFormType;
use App\Repository\TicketRepository;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DefaultController extends AbstractController
{
    const TICKET_SUCCESS_MESSAGE = 'Köszönjük szépen a kérdésedet! Válaszunkkal hamarosan keresünk a megadott e-mail címen.';
    const CREATE_SUCCESS = 'Sikeres létrehozás!';
    const CREATE_ERROR = 'Sikertelen létrehozás!';

    protected $hasher;
    
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }


    #[Route('/admin', name: 'app_ticket_list', methods: ['GET'])]
    public function ticketList(TicketRepository $ticketRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $tickets = $ticketRepository->findAll();
        return $this->paginateList($tickets, $paginator, $request, 'admin/ticket-list.html.twig');
    }


    #[Route('/admin/admin-list', name: 'app_admin_list', methods: ['GET'])]
    public function adminList(AdminRepository $adminRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $admins = $adminRepository->findAll();
        return $this->paginateList($admins, $paginator, $request, 'admin/admin-list.html.twig');
    }


    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function createTicket(Request $request, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $ticketDTO = new TicketFormDTO();
        $form = $this->createForm(TicketFormType::class, $ticketDTO);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticketEntity = new Ticket();
            $ticketEntity->setName($ticketDTO->getName());
            $ticketEntity->setEmail($ticketDTO->getEmail());
            $ticketEntity->setMessage($ticketDTO->getMessage());
    
            $entityManager->persist($ticketEntity);
            $entityManager->flush();
            $session->getFlashBag()->add('success', $this::TICKET_SUCCESS_MESSAGE);

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/contact.html.twig',[
            'form' => $form->createView()
        ]);
    }


    #[Route('/admin/create-admin', name: 'app_admin_create', methods: ['GET', 'POST'])]
    public function createAdmin(Request $request, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $adminDTO = new AdminFormDTO();
        $form = $this->createForm(AdminFormType::class, $adminDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminEntity = new Admin();
            $adminEntity->setUsername($adminDTO->getUsername());
            $adminEntity->setPassword($this->hasher->hashPassword($adminEntity, $adminDTO->getPassword()));
    
            $entityManager->persist($adminEntity);
            $entityManager->flush();
            $session->getFlashBag()->add('success', $this::CREATE_SUCCESS);

            return $this->redirectToRoute('app_admin_list');
        }

        return $this->render('admin/admin-create.html.twig',[
            'form' => $form->createView()
        ]);
    }

    protected function paginateList($entityList, $paginator, $request, $template) : Response
    {
        $result = $paginator->paginate(
            $entityList,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render($template, [
            'items' => $result,
        ]);
    }
    

    #[Route('/admin/{id}/edit', name: 'app_admin_edit')]
    public function editAdmin(Admin $admin, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(AdminFormType::class, $admin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($admin);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_list');
        }

        return $this->render('admin/admin-edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
