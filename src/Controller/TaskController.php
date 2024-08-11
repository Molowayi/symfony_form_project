<?php
// src/Controller/TaskController.php
namespace App\Controller;

use App\Entity\Task;
use App\Form\Type\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/myform', name: 'myform', defaults: ['page' => '1', '_format' => 'html'], methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $task = new Task();
        
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();

            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('task_success');
        }

      

        return $this->render('task/new.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/tasksuccess', name: 'task_success', defaults: ['page' => '1', '_format' => 'html'], methods: ['GET', 'POST'])]
    public function taskSuccess(){

        $message  = "Your form has been submitted";
        return $this->render("task/tasksuccess.html.twig", ["message" => $message,]);
    }

    #[Route('/product/edit/{id}', name: 'product_edit')]
    public function update(EntityManagerInterface $entityManager, int $id): Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }

        $product->setName('New product name!');
        $entityManager->flush();

        return $this->redirectToRoute('product_show', [
            'id' => $product->getId()
        ]);
    }

    
}

?>