<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Todo;
use App\Form\TodoType;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\Base;

class TodoController extends AbstractController
{
    private $utils;

    public function __construct(Base $utils)
    {
        $this->utils = $utils;
    }

    #[Route('/todo', name: 'todo.index')]
    public function index(ManagerRegistry $doctrine)
    {
        $todos = $doctrine->getRepository(Todo::class)->findAll();
        $this->utils->notFoundError($todos, 'todo');

        return $this->render('todo/index.html.twig', [
            'title' => 'List of todos',
            'todos' => $todos
        ]);
    }

    #[Route('/todo/create', name: 'todo.create')]
    public function create(ManagerRegistry $doctrine, Request $request)
    {
        $form = $this->createForm(TodoType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $todoTmp = $form->getData();
                $em = $doctrine->getManager();
                $em->persist($todoTmp);
                $em->flush();

                return $this->utils->redirectWithMessage('todo.index', 'notice', 'Todo saved successfully.');
            
            } catch (\Exception $exception) {
                return $this->utils->redirectWithMessage('todo.index', 'error', 'Failed to save todo.');
            }
        }

        return $this->render('todo/form.html.twig', [
            'title' => 'Create new todo:',
            'form' => $form->createView()
        ]);
    }


    #[Route('/todo/close/{id}', name: 'todo.close')]
    public function closeTodo(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $todo = $doctrine->getRepository(Todo::class)->find($id);
        // throws exception
        $this->utils->notFoundError($todo, 'todo', $id);

        try {
            $em = $doctrine->getManager();
            $todo->setStatus('done');
            $em->persist($todo);
            $em->flush();

            $this->addFlash('notice', 'Todo closed succesfully.');
            return $this->redirectToRoute('todo.index');
        } catch (\Exception $exception) {
            $this->addFlash('notice', 'Failed to close todo.');
            return $this->redirectToRoute('todo.index');
        }
    }


    #[Route('/todo/update/{id}', name: 'todo.update')]
    public function updateTodo(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $todo = $doctrine->getRepository(Todo::class)->find($id);
        // throws exception
        $this->utils->notFoundError($todo, 'todo', $id);

        $form = $this->createForm(TodoType::class);
        $form->setData($todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $todoTmp = $form->getData();
                $em = $doctrine->getManager();
                $em->persist($todoTmp);
                $em->flush();

                return $this->utils->redirectWithMessage('todo.index', 'notice', 'Todo updated successfully.');
            } catch (\Exception $exception) {
                return $this->utils->redirectWithMessage('todo.index', 'error', 'Failed to update todo.');
            }
        }

        return $this->render('todo/form.html.twig', [
            'title' => 'Update todo:',
            'form' => $form->createView()
        ]);
    }


    #[Route('/todo/delete/{id}', name: 'todo.delete')]
    public function deleteTodo(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();

        $todo = $entityManager->getRepository(Todo::class)->find($id);

        // throws exception
        $this->utils->notFoundError($todo, 'todo', $id);

        // delete the field
        $entityManager->remove($todo);
        $entityManager->flush();

        return $this->utils->redirectWithMessage('todo.index', 'notice', 'Todo deleted successfully.');
    }
}
