<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceType;
use App\Form\SearchAnnouncementType;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\CommentRepository;
use Symfony\Component\Security\Core\Security;


#[Route('/annonce')]
class AnnonceController extends AbstractController
{



    #[Route('/search', name: 'annonce_search', methods: ['GET', 'POST'])]
    public function search(Request $request, AnnonceRepository $annonceRepository): Response
    {
        $form = $this->createForm(SearchAnnouncementType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->get('query')->getData();

            // Debugging information
            dump($query);

            // Perform the search logic (customize this according to your needs)
            $annonces = $annonceRepository->search($query);

            return $this->render('annonce/search_results.html.twig', [
                'annonces' => $annonces,
            ]);
        }

        return $this->render('annonce/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/', name: 'app_annonce_index', methods: ['GET'])]
    public function index(Request $request, AnnonceRepository $annonceRepository, PaginatorInterface $paginator): Response
    {   $searchForm = $this->createForm(SearchAnnouncementType::class);
        $searchForm->handleRequest($request);
        $query = $annonceRepository->createQueryBuilder('a')
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('annonce/index.html.twig', [
            'pagination' => $pagination,
            'form' => $searchForm->createView()
        ]);
    }

    #[Route('/new', name: 'app_annonce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $annonce = new Annonce();

        // Get the current user
        $user = $security->getUser();

        // Set the current user as the creator of the announcement
        if ($user) {
            $annonce->setUser($user);
        }

        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annonce/new.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_annonce_show', methods: ['GET'])]
    public function show(Annonce $annonce, CommentRepository $commentRepository,  Request $request,PaginatorInterface $paginator): Response
    {
        $comments = $commentRepository->findByAnnonceId($annonce->getId());

        $pagination = $paginator->paginate(
            $comments,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
            'pagination' => $pagination,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_annonce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annonce/edit.html.twig', [
            'annonce' => $annonce,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_annonce_delete', methods: ['POST'])]
    public function delete(Request $request, Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annonce->getId(), $request->request->get('_token'))) {
            $entityManager->remove($annonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_annonce_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/annonces-by-user/{id}', name: 'annonces_by_user')]
    public function getAnnoncesByUser(AnnonceRepository $annonceRepository, int $id): Response
    {
        $pagination = $annonceRepository->findByUserId($id);

        // Now $annonces contains all annonces created by the user with $userId

        // Add your logic here...

        return $this->render('annonce/index3.html.twig', ['pagination' => $pagination]);
    }


    /**
     * @Route("/annonces-by-category/{categoryId}", name="annonces_by_category")
     * @param AnnonceRepository $annonceRepository
     * @param int $categoryId
     * @return Response
     */
    public function annoncesByCategory(AnnonceRepository $annonceRepository, int $categoryId): Response
    {
        $pagination = $annonceRepository->findByCategoryId($categoryId);

        // Do something with the $annonces, for example, pass it to a template
        // ...

        return $this->render('annonce/index2.html.twig', ['pagination' => $pagination]);
    }




}


