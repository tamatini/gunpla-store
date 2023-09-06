<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function categoryIndex(
        EntityManagerInterface $entityManager
    ) : Response {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        return $this->render('category/index.html.twig', [
            'title'=>'Toutes les catégories',
            'categories' => $categories
        ]);
    }

    #[Route('/{id}', name:'detail')]
    public function getCategory(
        EntityManagerInterface $entityManager,
        Category $category
    ): Response {
        $category = $entityManager->getRepository(Category::class)->findOneBy(['id'=>$category->getId()]);
        if (!$category) {
            throw $this->createNotFoundException('Cette catégorie n\'existe pas');
        }
        return $this->render('category/detail.html.twig', [
            'title' => $category->getName(),
            'category' => $category
        ]);
    }


}
