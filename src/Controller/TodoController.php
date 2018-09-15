<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TodoController extends AbstractController
{
    /**
     * @Route("/todos", methods={"GET"})
     */
    public function getList()
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
        ]);
    }

    /**
     * @Route("/todos/{id}", methods={"GET"})
     */
    public function getOne()
    {
        return $this->json([
            'message' => 'get one',
        ]);
    }

    /**
     * @Route("/todos", methods={"POST"})
     */
    public function create()
    {
        return $this->json([
            'create' => 'yay'
        ]);
    }

    /**
     * @Route("/todos/{id}", methods={"PATCH"})
     */
    public function update($id)
    {
        return $this->json([
            'id' => $id,
        ]);
    }

    /**
     * @Route("/todos/{id}", methods={"DELETE"})
     */
    public function delete($id)
    {
        return $this->json([
            'id' => $id,
        ]);
    }
}
