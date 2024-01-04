<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Annonce;


#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index', methods: ['GET'])]
    public function index(Request $request, CommentRepository $commentRepository, PaginatorInterface $paginator): Response
    {
        $query = $commentRepository->createQueryBuilder('c')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('comment/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $annonce = $comment->getAnnonce();

            // Assuming you have a route named 'app_annonce_show' for displaying annonce details
            $annonceRoute = $this->generateUrl('app_annonce_show', ['id' => $annonce->getId()]);

            return $this->redirect($annonceRoute);

        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_delete', methods: ['POST'])]
    public function delete(Request $request, Comment $comment, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        $annonce = $comment->getAnnonce();

        // Assuming you have a route named 'app_annonce_show' for displaying annonce details
        $annonceRoute = $this->generateUrl('app_annonce_show', ['id' => $annonce->getId()]);

        return $this->redirect($annonceRoute);
    }

    /**
     * @Route("/comments-by-user/{id}", name="comments_by_user")
     * @param AnnonceRepository $annonceRepository
     * @param int $id
     * @param PaginatorInterface $paginator
     * @param Request $request

     * @return Response
     */
    #[Route('/comments-by-user/{id}', name: 'comments_by_user')]
    public function commentsByUser(Request $request,CommentRepository $commentRepository, int $id, PaginatorInterface $paginator): Response
    {
        $comments = $commentRepository->findByUserId($id);

        // Do something with the $comments, for example, pass it to a template
        // ...
        $pagination = $paginator->paginate(
            $comments,
            $request->query->getInt('page', 1), // Current page number, 1 by default
            10 // Number of items per page
        );
        return $this->render('comment/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/comments-by-annonce/{id}', name: 'comments_by_annonce')]
    public function commentsByAnnonce(Request $request,CommentRepository $commentRepository, int $id, PaginatorInterface $paginator): Response
    {
        $comments = $commentRepository->findByAnnonceId($id);

        $pagination = $paginator->paginate(
            $comments,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('comment/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/{annonceId}/add-comment', name: 'app_comment_add',  methods: ['GET', 'POST'])]
    public function newc(Request $request, int $annonceId, EntityManagerInterface $entityManager): Response
    {
        // Get the current user
        $user = $this->getUser();

        // Check if the user is authenticated
        if (!$user) {
            // Redirect to login or handle the scenario where the user is not authenticated
            // You can customize this part based on your authentication logic
            return $this->redirectToRoute('app_login');
        }

        // Create a new comment and set the user and annonce
        $comment = new Comment();
        $comment->setUser($user);

        // You can also fetch the Annonce entity using the $annonceId and set it to the comment
        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);
        $comment->setAnnonce($annonce);

        // Handle form submission
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();

            // Redirect to the annonce show page after adding the comment
            return $this->redirectToRoute('app_annonce_show', ['id' => $annonceId]);
        }

        // If form submission is unsuccessful or it's a GET request, render the newc template
        return $this->render('comment/newc.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            $form->remove('user'),
            $form->remove('annonce'),

            'annonce' => $annonce,
        ]);
    }
}
