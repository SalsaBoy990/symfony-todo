<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;

class TodoController extends AbstractController
{
    #[Route('/todo', name: 'todo.index')]
    public function index(ManagerRegistry $doctrine)
    // public function listTodo(TodoRepository $todoRepository): Response
    {
        $todos = $doctrine->getRepository(Todo::class)->findAll();
        if (!count($todos)) {
            throw $this->createNotFoundException(
                'No todos found'
            );
        }

        return $this->render('todo/list.html.twig', [
            'title' => 'List of todos',
            'todos' => $todos
        ]);


        /*$todos = $doctrine->getRepository(Todo::class)->findBy([
            'name' => 'Learn Laravel and Symfony'
        ]);*/

        //$todos = $doctrine->getRepository(Todo::class)->findByNameField('Laravel');

        /*$todos = $todoRepository->findByName('Laravel');

        if (!count($todos)) {
            throw $this->createNotFoundException(
                'No todos found'
            );
        }

        foreach ($todos as $todo) {
            echo $todo->getName();
            echo '<br>';
        }

        return new Response('Number of todos found: ' . count($todos));*/
    }


    #[Route('/todo/test', name: 'todo_create')]
    public function createTodo(ManagerRegistry $doctrine)
    //public function createTodo(ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();

        /*$todo = new Todo();
        $todo
            ->setStatus('ongoing')
            ->setPriority('low')
            ->setName('Learn PHP')
            ->setStatus('ongoing')
            ->setDateCreation(new \DateTime());

        $em->persist($todo);
        $em->flush();*/


        /*return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TestController.php',
        ]);*/
    }


    #[Route('/todo/create', name: 'todo.create')]
    public function create(ManagerRegistry $doctrine, Request $request)
    {
        $form = $this->createForm(TodoType::class);
        /*$form = $this->createFormBuilder()
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->getForm();*/

        $form->handleRequest($request);

        /*if ($_POST) {
            print_r($_REQUEST);
            echo 'Submit it';
        }*/
        if ($form->isSubmitted() && $form->isValid()) {
            $todoTmp = $form->getData();
            dump($todoTmp->getName());

            $em = $doctrine->getManager();
            $em->persist($todoTmp);
            $em->flush();
        }

        return $this->render('todo/form.html.twig', [
            'title' => 'Create new todo:',
            'form' => $form->createView()
        ]);
    }


    #[Route('/todo/show/{id}', name: 'todo.show')]
    public function showTodo(ManagerRegistry $doctrine, int $id)
    {
        $todo = $doctrine->getRepository(Todo::class)->find($id);

        if (!$todo) {
            throw $this->createNotFoundException(
                'No todo found for id ' . $id
            );
        }

        return $this->render('todo/show.html.twig', [
            'title' => $todo->getName(),
            'todo' => $todo
        ]);
        //return new Response('Todo details: ' . $todo->getName());
    }


    #[Route('/todo/update/{id}', name: 'todo.update')]
    public function updateTodo(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $todo = $doctrine->getRepository(Todo::class)->find($id);
        if (!$todo) {
            throw $this->createNotFoundException(
                'No todo found for id ' . $id
            );
        }

        $form = $this->createForm(TodoType::class);
        $form->setData($todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $todoTmp = $form->getData();
            dump($todoTmp->getName());

            $em = $doctrine->getManager();
            $em->persist($todoTmp);
            $em->flush();
        }

        return $this->render('todo/form.html.twig', [
            'title' => 'Update todo:',
            'form' => $form->createView()
        ]);
        /* $entityManager = $doctrine->getManager();
        $todo = $entityManager->getRepository(Todo::class)->find($id);

        if (!$todo) {
            throw $this->createNotFoundException(
                'No todo found for id ' . $id
            );
        }

        // update the field
        $todo->setPriority('high');
        $entityManager->flush();


        return new Response('Todo updated: ' . $todo->getName());*/
    }


    #[Route('/todo/delete/{id}', name: 'todo.delete')]
    public function deleteTodo(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();

        $todo = $entityManager->getRepository(Todo::class)->find($id);
        if (!$todo) {
            throw $this->createNotFoundException(
                'No todo found for id ' . $id
            );
        }

        // delete the field
        $entityManager->remove($todo);
        $entityManager->flush();

        return new Response('Todo deleted: ' . $todo->getName());
    }
}
