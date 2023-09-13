<?php

namespace App\Controller\Admin;

use App\Entity\CartItem;
use App\Entity\Images;
use App\Entity\Product;
use App\Form\ProductType;
use App\Service\PictureService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/product', name: 'admin_product_')]
class ProductCrudController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        try {
            $products = $entityManager->getRepository(Product::class)->findAll();
        } catch (Exception $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
        return $this->render('admin/product/list.html.twig', [
            'title' => 'Liste des produits',
            'products' => $products
        ]);
    }

    #[Route('/new', name: 'new')]
    public function newProduct(
        EntityManagerInterface $entityManager,
        Request                $request,
        PictureService         $pictureService
    ): Response
    {
        $product = new Product();
        $productForm = $this->createForm(ProductType::class, $product);
        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $images = $productForm->get('images')->getData();
            foreach ($images as $image) {
                $folder = 'products';
                $file = $pictureService->addImage($image, $folder, 300, 300);
                $img = new Images();
                $img->setName($file);
                $product->addImage($img);
            }
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash("success", "L'article à bien été rajouter");
            return $this->redirectToRoute('admin_product_list');
        }
        return $this->render('admin/product/new.html.twig', [
            'title' => 'Ajouter un nouveau produit',
            'product_form' => $productForm->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'put')]
    public function putProduct(
        EntityManagerInterface $entityManager,
        Request                $request,
        int                    $id,
        PictureService         $pictureService
    ): Response
    {
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);
        if (!$product) {
            throw $this->createNotFoundException(
                'Ce produit n\'existe pas'
            );
        }
        $productForm = $this->createForm(ProductType::class, $product);
        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $images = $productForm->get('images')->getData();
            foreach ($images as $image) {
                $folder = 'products';
                $file = $pictureService->addImage($image, $folder, 300, 300);
                $img = new Images();
                $img->setName($file);
                $product->addImage($img);
            }
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('success', 'Le produit à bien été mis à jour');
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }
        return $this->render('admin/product/edit.html.twig', [
            'title' => 'Mettre à jour le produit',
            'product_form' => $productForm,
            'product' => $product
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function deleteProduct(
        EntityManagerInterface $entityManager,
        Product                $product,
        Request                $request
    ): Response
    {
        $cartItems = $entityManager->getRepository(CartItem::class)->findBy(['product' => $product]);
        foreach ($cartItems as $cartItem) {
            $entityManager->remove($cartItem);
        }
        $entityManager->remove($product);
        $entityManager->flush();
        $this->addFlash('success', 'Le produit à bien été supprimé');
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    #[Route('/delete/image/{id}', 'delete_image')]
    public function deleteImage(
        Images                 $images,
        Request                $request,
        EntityManagerInterface $entityManager,
        PictureService         $pictureService
    ): Response
    {
        $image = $entityManager->getRepository(Images::class)->findOneBy(['id' => $images->getId()]);
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->get('token'))) {
            $name = $image->getName();
            if ($pictureService->deleteImage($name, 'products', 300, 300)) {
                $entityManager->remove($image);
                $entityManager->flush();
                $this->addFlash('success', 'L\'image à été supprimer avec succès');
            }
            $this->addFlash('error', 'Erreur de suppression');
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }
        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}