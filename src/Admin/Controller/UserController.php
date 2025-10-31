<?php

namespace App\Admin\Controller;

use App\Admin\Form\UserType;
use App\Admin\Security\Voter\UserVoter;
use App\Admin\Service\UserService;
use App\Admin\Service\UserStoreService;
use App\Entity\User;
use App\Entity\UserStore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
final class UserController extends AbstractController {

    public function __construct(private UserService $userService, private UserStoreService $userStoreService) {
    }

    #[Route('', name: 'app_user_index', methods: ['GET'])]
    public function index(): Response {
        $this->denyAccessUnlessGranted(UserVoter::VIEW, new User());

        $users = $this->userService->listUsers($this->getUser());

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/cadastro', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response {
        $user = new User();
        $this->denyAccessUnlessGranted(UserVoter::CREATE, $user);

        /** @var User $loggedUser */
        $loggedUser = $this->getUser();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            $this->userService->saveUser($user, $plainPassword, 'cadastrado');

            if (in_array('ROLE_STORE_OWNER', $loggedUser->getRoles()))
                $this->userStoreService->attach($user, $loggedUser->getUserStores()->first()->getStore());

            $this->addFlash('success', 'Usuário cadastrado com sucesso!');

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editar', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response {
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            $this->userService->saveUser($user, $newPassword, 'atualizado');
            $this->addFlash('success', 'Usuário atualizado com sucesso!');

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/perfil', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request): Response {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            $this->userService->saveUser($user, $newPassword, 'perfil atualizado');
            $this->addFlash('success', 'Perfil atualizado com sucesso!');

            return $this->redirectToRoute('app_user_profile', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('admin/user/profile.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response {
        $this->denyAccessUnlessGranted(UserVoter::DELETE, $user);

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->userService->deleteUser($user, $this->getUser());
            $this->addFlash('success', 'Usuário excluído com sucesso.');
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
