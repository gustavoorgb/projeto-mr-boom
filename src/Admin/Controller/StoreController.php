<?php

namespace App\Admin\Controller;

use App\Entity\Store;
use App\Admin\Form\StoreType;
use App\Admin\Form\UserStoreType;
use App\Admin\Service\StoreService;
use App\Admin\Service\UserStoreService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/loja')]
final class StoreController extends AbstractController {
    public function __construct(private StoreService $storeService, private UserStoreService $userStoreService) {
    }

    #[Route(name: 'app_store_index', methods: ['GET'])]
    public function index(): Response {
        $stores = $this->storeService->listStores($this->getUser());
        return $this->render('admin/store/index.html.twig', [
            'stores' => $stores
        ]);
    }

    #[Route('/{id}/vincular', name: 'app_store_link', methods: ['GET', 'POST'])]
    public function linkUser(Request $request, Store $store): Response {
        $form = $this->createForm(UserStoreType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->userStoreService->attach($data->getUser(), $store, $data);

            $this->addFlash('success', 'Usúario vinculado à loja com sucesso!');
        }

        return $this->render('admin/store/link.html.twig', [
            'store' => $store,
            'form' => $form,
        ]);
    }

    #[Route('/selecionar', name: 'app_store_select', methods: ['GET'])]
    public function select(): Response {
        $stores = $this->storeService->listStores($this->getUser());

        if (count($stores) === 0) {
            $this->addFlash('warning', 'Você não está associado a nenhuma loja.');
            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/store/select.html.twig', [
            'stores' => $stores,
        ]);
    }

    #[Route('/setar/{id}', name: 'app_store_set', methods: ['GET', 'POST'])]
    public function set(Store $store): Response {
        $this->storeService->selectStore($store, $this->getUser());

        $this->addFlash('success', 'Loja "' . $store->getCorporateName() . '" selecionada com sucesso.');

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/cadastro', name: 'app_store_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response {
        $store = new Store();
        $form = $this->createForm(StoreType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->storeService->saveStore($store, $this->getUser(), 'cadastrada');
            $this->addFlash('success', 'Loja cadastrada com sucesso!');

            return $this->redirectToRoute('app_store_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/store/new.html.twig', [
            'store' => $store,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_store_show', methods: ['GET'])]
    public function show(Store $store): Response {
        return $this->render('admin/store/show.html.twig', [
            'store' => $store,
        ]);
    }

    #[Route('/{id}/editar', name: 'app_store_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Store $store): Response {
        $form = $this->createForm(StoreType::class, $store);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->storeService->saveStore($store, $this->getUser(), 'atualizada');
            $this->addFlash('success', 'Loja atualizada com sucesso!');

            return $this->redirectToRoute('app_store_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/store/edit.html.twig', [
            'store' => $store,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_store_delete', methods: ['POST'])]
    public function delete(Request $request, Store $store): Response {
        if ($this->isCsrfTokenValid('delete' . $store->getId(), $request->getPayload()->getString('_token'))) {
            $this->storeService->deleteStore($store, $this->getUser());
            $this->addFlash('success', 'Loja excluída com sucesso!');
        }

        return $this->redirectToRoute('app_store_index', [], Response::HTTP_SEE_OTHER);
    }
}
