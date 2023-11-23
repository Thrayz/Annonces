<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);

        $em->persist($user);
        $em->flush();

        return new Response('User created!', Response::HTTP_CREATED);
    }

    #[Route('/users/{id}', name: 'update_user', methods: ['PUT'])]
    public function updateUser(Request $request, EntityManagerInterface $em, $id): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);

        $em->flush();

        return new Response('User updated!', Response::HTTP_OK);
    }

    #[Route('/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(EntityManagerInterface $em, $id): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return new Response('User deleted!', Response::HTTP_OK);
    }

    #[Route('/users', name: 'list_users', methods: ['GET'])]
    public function listUsers(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        $responseData = [];
        foreach ($users as $user) {
            $responseData[] = [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
            ];
        }

        return $this->json($responseData);
    }

    #[Route('/users/{id}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserById(EntityManagerInterface $em, $id): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {

            return $this->json(['error' => 'User not found'], 404);
        }

        $responseData = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ];

        return $this->json($responseData);
    }
}
