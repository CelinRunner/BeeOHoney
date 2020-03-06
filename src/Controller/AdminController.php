<?php

namespace App\Controller;

use App\Form\ConfirmDeletionType;
use App\Form\ProductType;
use App\Service\Uploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/product/", name="admin_product")
     */
    public function admin_product(ProductRepository $pRepo, CategoryRepository $cRepo)
    {
        $products = $pRepo->findAll();
        $categories = $cRepo->findAll();

        return $this->render('admin/product_list.html.twig', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/product/add", name="admin_product_add")
     */
    public function adminProductAdd(Request $request, Uploader $uploader)
    {
        $manager = $this -> getDoctrine() -> getManager();
        //Creation u formulaire
        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);
        //Traiement du formulaire
        if($form->isSubmitted() && $form->isValid()){
           $product = $form->getData();
           $image = $form['imageUpload']->getData();

           $product->setImage($uploader->savePhoto($image));
           $manager-> persist($product);
           $manager->flush();

           $this->addFlash('success', 'Le produit a été enregistré');
        }


        return $this->render('admin/product_form.html.twig', [
            'form' => $form->createView(),
            'form_title' =>'Ajouter un produit',
        ]);
    }

    /**
     * @Route("/admin/product/update/{id}", name="admin_product_update")
     */
    public function adminProductUpdate(Product $product, Request $request, Uploader $uploader)
    {
        $manager = $this -> getDoctrine() ->getManager();

        //Instanciation du formulaire

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        //Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
            $image = $form['imageUpload']->getData();

            if ($image !== null){
                $product->setImage($uploader->replacePhoto($image, $product->getImage()));
            }

            $manager->persist($product);
            $manager->flush();

            $this->addFlash('success', 'Le produit a été modifié');
        }

        return $this->render('admin/product_form.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'form_title' => 'Modifier un produit',
        ]);
    }

    /**
     * @Route("/admin/product/delete/{id}", name="admin_product_delete")
     */
    public function adminProductDelete(Product $product, Request $request, Uploader $uploader)
    {

        $manager = $this -> getDoctrine() ->getManager();

        //Instanciation du formulaire

        $form = $this->createForm(ConfirmDeletionType::class);
        $form->handleRequest($request);

        // Traitement du formulaire

        if ($form->isSubmitted() && $form->isValid()){
            $uploader->deletePhoto($product->getImage());
            $manager->remove($product);
            $manager->flush();

            $this->addFlash('success', 'Le produit a été supprimé');
            return $this->redirectToRoute('admin_product');
        }


        return $this->render('admin/delete.html.twig', [
            'form' => $form->createView(),
            'title'=>'Supprimer un produit',
            'form_label' =>'Je confirme la suppression du produit' . $product->getTitle(),
            'cancel_route' => 'admin_product',
        ]);
    }

    /**
     * @Route("/admin/membre/", name="admin_user")
     */
    public function adminUser(UserRepository $uRepo)
    {
        $users = $uRepo->findAll();

        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admin/membre/add", name="admin_user_add")
     */
    public function adminUserAdd(Request $request, Uploader $uploader)
    {
        $manager = $this -> getDoctrine() -> getManager();
        //Creation u formulaire
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        //Traiement du formulaire
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $manager-> persist($user);
            $manager->flush();

            $this->addFlash('success', 'La personne a bien été enregistrée');
        }


        return $this->render('admin/product_form.html.twig', [
            'form' => $form->createView(),
            'form_title' =>'Ajouter un produit',
        ]);
    }
}
