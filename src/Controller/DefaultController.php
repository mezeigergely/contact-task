<?php

namespace App\Controller;

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

    private $hasher;
    private $entityManager;
    
    public function __construct(UserPasswordHasherInterface $hasher, EntityManagerInterface $entityManager)
    {
        $this->hasher = $hasher;
        $this->entityManager = $entityManager;
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
    public function createTicket(Request $request, SessionInterface $session)
    {
        $ticketDTO = new TicketFormDTO();
        $form = $this->createForm(TicketFormType::class, $ticketDTO);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticketEntity = new Ticket();
            $ticketEntity->setName($ticketDTO->getName());
            $ticketEntity->setEmail($ticketDTO->getEmail());
            $ticketEntity->setMessage($ticketDTO->getMessage());
    
            $this->entityManager->persist($ticketEntity);
            $this->entityManager->flush();
            $session->getFlashBag()->add('success', $this::TICKET_SUCCESS_MESSAGE);

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/contact.html.twig',[
            'form' => $form->createView()
        ]);
    }


    #[Route('/admin/create-admin', name: 'app_admin_create', methods: ['GET', 'POST'])]
    public function createAdmin(Request $request, SessionInterface $session)
    {
        $adminEntity = new Admin();
        $form = $this->createForm(AdminFormType::class, $adminEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adminEntity->setPassword($this->hasher->hashPassword($adminEntity, $adminEntity->getPassword()));
            $this->entityManager->persist($adminEntity);
            $this->entityManager->flush();
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
    public function editAdmin($id, Admin $adminEntity, Request $request, SessionInterface $session)
    {
        $adminRepository = $this->entityManager->getRepository(Admin::class);
        $queryBuilder = $adminRepository->createQueryBuilder('a');

        $existingAdmin = $queryBuilder
            ->where('a.username = :username')
            ->andWhere('a.id != :id')
            ->setParameter('username', $adminEntity->getUsername())
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $form = $this->createForm(AdminFormType::class, $adminEntity);
        $form->handleRequest($request);

        if (!$existingAdmin && $form->isSubmitted() && $form->isValid()) {
            $adminEntity->setPassword($this->hasher->hashPassword($adminEntity, $adminEntity->getPassword()));
            $this->entityManager->flush();
            $session->getFlashBag()->add('success', $this::CREATE_SUCCESS);

            return $this->redirectToRoute('app_admin_list');
        }

        return $this->render('admin/admin-edit.html.twig', [
            'form' => $form->createView(),
            'id' => $id
        ]);
    }
}
