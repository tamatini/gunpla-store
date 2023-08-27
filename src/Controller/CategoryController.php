<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/admin', name: 'admin')]
    public function categoryAdmin(
        EntityManagerInterface $entityManager
    ) : Response {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        return $this->render('category/admin.html.twig', [
            'title'=>'Gestion des catégories',
            'categories'=>$categories
        ]);
    }


    #[Route('/new', name: 'new')]
    public function newCategory(
        EntityManagerInterface $entityManager,
        Request $request
    ) : Response
    {
        $category = new Category();
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);
        if ($categoryForm->isValid() && $categoryForm->isSubmitted()) {
            $entityManager->persist($categoryForm);
            $entityManager->flush();
            $this->addFlash('success', "La catégorie à bien été ajoutée");
            return $this->redirectToRoute('category_new');
        }
        return $this->render('category/new.html.twig', [
            'title'=>'Ajouter une catégorie',
            'category_form'=>$categoryForm->createView()
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/{id}', name:'detail')]
    public function getCategory(
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $category = $entityManager->getRepository(Category::class)->findOneById($id);
        if (!$category) {
            throw $this->createNotFoundException(
                'Cette catégorie n\'existe pas'
            );
        }
        return $this->render('category/detail.html.twig', [
            'title' => $category->getName(),
            'category' => $category
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/{id}/delete')]
    public function deleteCategory(
        EntityManagerInterface $entityManager,
        int $id
    ) : Response {
        $category = $entityManager->getRepository(Category::class)->findOneById($id);
        if ($category) {
            throw $this->createNotFoundException(
                'Cette catégorie n\'existe pas'
            );
        }
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('category_admin');
    }

    public function updateCategory(
        EntityManagerInterface $entityManager,
        int $id,
        Request $request
    ) : Response {
        $category = $entityManager->getRepository(Category::class)->findOneById($id);
        if ($category) {
            throw $this->createNotFoundException(
                'Cette catégories n\'existe pas'
            );
        }
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);
        if ($categoryForm->isValid() && $categoryForm->isSubmitted()) {
            $entityManager->persist($categoryForm);
            $entityManager->flush();
            $this->addFlash('success', 'La catégorie à été mise à jour');
            return $this->redirectToRoute('category_admin');
        }
        return $this->render('category/form.html.twig', [
            'title' => 'Mettre à jour la catégorie',
            'categoryForm' => $categoryForm->createView()
        ]);
    }
}
