<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractFOSRestController
{
    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->handleView($this->view($userRepository->findAll(), Response::HTTP_OK));
    }

    /**
     * @Route("/new", name="app_user_new", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);
            $userRepository->add($user, true);

            return $this->handleView($this->view($user, Response::HTTP_OK));
        }

        return $this->handleView($this->view($form, Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_user_edit", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @param UserRepository $userRepository
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    public function edit(Request $request, User $user, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($hashedPassword);

            $userRepository->add($user, true);

            return $this->handleView($this->view($user, Response::HTTP_OK));
        }

        return $this->handleView($this->view($form, Response::HTTP_BAD_REQUEST));
    }

    /**
     * @Route("/{id}/delete", name="app_user_delete", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @param UserRepository $userRepository
     * @return Response
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        $userRepository->remove($user, true);

        return $this->handleView($this->view(null, Response::HTTP_NO_CONTENT));
    }
}
