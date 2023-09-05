<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'home_')]
class HomeController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/', name: 'index')]
    public function index(
        EntityManagerInterface $entityManager
    ): Response
    {
        $latestProduct = $entityManager->getRepository(Product::class)->findLatestProduct(8);
        return $this->render('home/index.html.twig', [
            'title'         => 'Accueil',
            'latestProducts' => $latestProduct
        ]);
    }
}
