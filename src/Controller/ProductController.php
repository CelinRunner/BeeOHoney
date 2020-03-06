<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
//use App\Repository\ProductRepository as PR;
//use App\Repository\CategoryRepository as CR;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{


    /**
     * @Route("/", name="home")
     */
//    public function index(PR $proRepo, CR $catRepo){
//        $products = $proRepo->findAll();
//        $categories = $catRepo->findAll();
//        return $this->render('product/index.html.twig');
//    }
//si on veut faire une selection particuliere comme tout les produits  en promotion par exemple, faire un findBy()
      public function index(){
          $pRepo = $this -> getDoctrine() -> getRepository(Product::class);
          $cRepo = $this -> getDoctrine() -> getRepository(Category::class);
          //1 : Récupérer les données
          $products = $pRepo -> findAll();
          $categories = $cRepo ->findAll();

          //2 : Afficher la vue
          return $this -> render ('product/index.html.twig', [
              'products' => $products,
              'categories' => $categories,
          ]);
      }


    /**
     * @Route ("/produit/{slug}", name="product")
     */
    public function product(Product $product)
    {
            return $this->render('product/show.html.twig',[
                'product'=> $product
                ]);
    }



    /**
     * @Route ("/categorie/{slug}", name="category")
     */
    public function category(Category $category){
        $cRepo = $this -> getDoctrine() -> getRepository(Category::class);
        $cateories = $cRepo -> findAll();

        return $this -> render ('product/index.html.twig', [
            'products' => $category -> getProducts (),
            'categories' => $cateories,
            ]);
    }

    /**
     * @Route("/recherche/", name="recherche")
     */
    public function search(Request $request){
        $term = $request -> query -> get ('s');

        $pRepo = $this -> getDoctrine() -> getRepository(Product::class);
        $cRepo = $this -> getDoctrine() -> getRepository(Category::class);

        $products = $pRepo -> findByTerm($term);
        $categories = $cRepo -> findAll();

        return $this->render('product/index.html.twig',[
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
