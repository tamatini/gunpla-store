<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Images;
use App\Form\CategoryType;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/category', name: 'admin_category_')]
class CategoryCrudController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(EntityManagerInterface $entityManager) : Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();
        return $this->render('admin/Category/list.html.twig', [
            'title'=>'Liste des catégories',
            'categories' => $categories
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(
        EntityManagerInterface $entityManager,
        Request $request,
        PictureService $pictureService
    ) : Response
    {
        $category = new Category();
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);
        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $images = $categoryForm->get('images')->getData();
            foreach ($images as $image) {
                $folder = 'categories';
                $file = $pictureService->addImage($image, $folder, 450, 300);
                $img = new Images();
                $img->setName($file);
                $category->setImageUrl($image);
            }
            $entityManager->persist($categoryForm);
            $entityManager->flush();
            $this->addFlash('success', "La catégorie à bien été ajoutée");
            return $this->redirectToRoute('admin_category_list');
        }
        return $this->render('admin/Category/new.html.twig', [
            'title'=>'Ajouter une catégorie',
            'category_form'=>$categoryForm->createView()
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(
        EntityManagerInterface $entityManager,
        Category $category
    ) : Response {
        $category = $entityManager->getRepository(Category::class)->findOneBy(['id'=>$category->getId()]);
        if ($category) {
            throw $this->createNotFoundException(
                'Cette catégorie n\'existe pas'
            );
        }
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('category_admin');
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function put(
        EntityManagerInterface $entityManager,
        Request $request,
        Category $category
    ) : Response {
        $category = $entityManager->getRepository(Category::class)->findOneBy(['id'=>$category->getId()]);
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
        return $this->render('admin/Category/edit.html.twig', [
            'title' => 'Mettre à jour la catégorie ' . $category->getName(),
            'categoryForm' => $categoryForm->createView()
        ]);
    }

}