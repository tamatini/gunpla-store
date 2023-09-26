<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'home_')]
class HomeController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/', name: 'index')]
    public function index(
        EntityManagerInterface $entityManager
    ): Response
    {
        $latestProduct = $entityManager->getRepository(Product::class)->findLatestProduct(8);
        $categories = $entityManager->getRepository(Category::class)->homepageCategory(6);
        $highGrades = $entityManager->getRepository(Product::class)->findByCategory("High Grade");
        return $this->render('home/index.html.twig', [
            'title' => 'Accueil',
            'latestProducts' => $latestProduct,
            'categories' => $categories,
            'highGrades' => $highGrades
        ]);
    }
}
