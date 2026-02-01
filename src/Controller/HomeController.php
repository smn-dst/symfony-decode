<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/annonce/{id}', name: 'app_property_show', requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        return $this->render('property/show.html.twig', ['id' => $id]);
    }
}
