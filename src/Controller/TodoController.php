<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
class TodoController extends AbstractController
{
    #[Route('/todo', name: 'app_todo')]
    public function index(SessionInterface $session): Response
    {
        $tasks = $session->get('tasks', []);
        return $this->render('todo/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }
    #[Route('/todo/add', name: 'app_todo_add', methods: ['POST'])]
    public function add(Request $request, SessionInterface $session): Response
    {
        $tasks = $session->get('tasks', []);
        $taskName = $request->request->get('task_name');
        if ($taskName) {
            $tasks[] = ['id' => uniqid('', true), 'name' => $taskName, 'completed' => false];
            $session->set('tasks', $tasks);
        }
        return $this->redirectToRoute('app_todo');
    }
    #[Route('/todo/delete/{id}', name: 'app_todo_delete')]
    public function delete(string $id, SessionInterface $session): Response
    {
        $tasks = $session->get('tasks', []);
        $tasks = array_filter($tasks, fn($task) => $task['id'] !== $id);
        $session->set('tasks', array_values($tasks));
        return $this->redirectToRoute('app_todo');
    }
    #[Route('/todo/toggle/{id}', name: 'app_todo_toggle')]
    public function toggle(string $id, SessionInterface $session): Response
    {
        $tasks = $session->get('tasks', []);
        foreach ($tasks as &$task) {
            if ($task['id'] === $id) {
                $task['completed'] = !$task['completed'];
                break;
            }
        }
        $session->set('tasks', $tasks);
        return $this->redirectToRoute('app_todo');
    }

    #[Route('/todo/clear', name: 'app_todo_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->set('tasks',[]);
        return $this->redirectToRoute('app_todo');
    }
}