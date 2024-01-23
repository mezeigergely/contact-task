<?php

namespace App\Controller;

use App\DTO\TicketFormDTO;
use App\Entity\Admin;
use App\Entity\Ticket;
use App\Form\AdminFormType;
use App\Form\TicketFormType;
use App\Repository\TicketRepository;
use App\Repository\AdminRepository;
use App\Service\DefaultService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DefaultController extends AbstractController
{
    private $hasher;
    private $entityManager;
    private $ticketRepository;
    private $adminRepository;
    private $defaultService;
    
    public function __construct(
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $entityManager,
        TicketRepository $ticketRepository,
        AdminRepository $adminRepository,
        DefaultService $defaultService)
    {
        $this->hasher = $hasher;
        $this->entityManager = $entityManager;
        $this->ticketRepository = $ticketRepository;
        $this->adminRepository = $adminRepository;
        $this->defaultService = $defaultService;
    }


    #[Route('/admin/ticket-list', name: 'app_ticket_list', methods: ['GET'])]
    public function ticketList(Request $request): Response
    {
        return $this->paginateList($this->ticketRepository, $request, 'admin/ticket-list.html.twig');
    }


    #[Route('/admin/admin-list', name: 'app_admin_list', methods: ['GET'])]
    public function adminList(Request $request): Response
    {
        return $this->paginateList($this->adminRepository, $request, 'admin/admin-list.html.twig');
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
            $session->getFlashBag()->add('success', $ticketDTO::TICKET_SUCCESS_MESSAGE);

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
            $adminEntity->setRoles(['ROLE_USER']);
            $hashedPassword = $this->hasher->hashPassword($adminEntity, $adminEntity->getPassword());
            $adminEntity->setPassword($hashedPassword);
            $this->entityManager->persist($adminEntity);
            $this->entityManager->flush();
            $session->getFlashBag()->add('success', $adminEntity::CREATE_SUCCESS);

            return $this->redirectToRoute('app_admin_list');
        }

        return $this->render('admin/admin-create.html.twig',[
            'form' => $form->createView()
        ]);
    }
    

    #[Route('/admin/{id}/edit', name: 'app_admin_edit')]
    public function editAdmin($id, Admin $adminEntity, Request $request, SessionInterface $session)
    {
        $adminRepository = $this->entityManager->getRepository(Admin::class);
        $queryBuilder = $adminRepository->createQueryBuilder('a');

        $existingAdmin = $this->defaultService->existingAdmin($queryBuilder, $adminEntity, $id);

        $form = $this->createForm(AdminFormType::class, $adminEntity);
        $form->handleRequest($request);

        if (!$existingAdmin && $form->isSubmitted() && $form->isValid()) {
            $adminEntity->setPassword($this->hasher->hashPassword($adminEntity, $adminEntity->getPassword()));
            $this->entityManager->flush();
            $session->getFlashBag()->add('success', $adminEntity::CREATE_SUCCESS);

            return $this->redirectToRoute('app_admin_list');
        }

        return $this->render('admin/admin-edit.html.twig', [
            'form' => $form->createView(),
            'id' => $id
        ]);
    }

    protected function paginateList($repository, $request, $template) : Response
    {
        $queryBuilder = $repository->createQueryBuilder('q');
        $queryBuilder->orderBy('q.id', 'ASC');
        $limit = 5;
        $page = $request->get('page', 1);
        $paginator = new Paginator($queryBuilder);
        $paginator
            ->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit)
        ;
        $total = $paginator->count();
        $lastPage = (int) ceil($total / $limit);

        return $this->render($template, [
            'paginator' => $paginator,
            'total' => $total,
            'lastPage' => $lastPage,
            'page' => $page,
        ]);
    }
}
