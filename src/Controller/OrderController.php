<?php

namespace App\Controller;

use App\Entity\Product;
//use http\Env\Request;
use Flosch\Bundle\StripeBundle\Stripe\StripeClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Order;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index()
    {
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

//("/panier", name="panier") ->  order/cart.html.twig
//'Je suis dans la page d'inscription'
//
//      ("cart/add/{id}") -> --- fonction vide ----
    /**
     * @Route ("/panier", name="cart")
     */
    public function panier(){
        return $this->render('order/cart.html.twig');
    }

    /**
     * @Route ("cart/add/{id}", name="cart_add")
     */

    public function cartAdd(Product $product, Request $request){
        $qty = $request->request->get('qty'); // $_POST['qty']
        $session = $request -> getSession(); // on recupere la session

        if(!$session -> has('panier')){
            $session -> set('panier', []); //Dans la session on construit un array vide (pa,oer) si il n'existe pas deja
        }

        $panier = $session -> get('panier');

        //Modifier la quantité d'un produit si il existe déjà (éviter les doublons de ligne)

         # Récupérer la liste des identifiancts des produits du panier
        $id_produits = array_map(function($prod){
            return $prod['product']-> getId();
        }, $panier);
         #Rechercher la clé du produit à ajouter au panier (si déjà existant)
        $cle_produit = array_search($product->getId(), $id_produits);


        # Si une clé a été trouvée (si le produit a ajouter existe déjà dans le panier)
        if($cle_produit !== false){
            #On incrémente la quantité
            $panier[$cle_produit]['quantity'] += $qty;
        }else{
            #Sinon on ajoute une nouvelle entrée dans le panier
            $panier[] = [
                'product' => $product,
                'quantity' => $qty
            ];
        }


        $session -> set('panier', $panier);

        $this -> addFlash('success', 'Le produit <b>' . $product -> getTitle() . '</b> a bien été ajouté au panier !<br> Vous pouvez <u><a href="' . $this -> generateUrl('home') . '">Continuer vos achats</a></u> ou accéder au <u><a href ="' . $this -> generateUrl('cart') . '">Panier</a></u>');

        return $this -> redirectToRoute('product', [
           'slug' => $product -> getSlug()
        ]);
    }

    /**
     * @Route ("cart_delete", name="cart_delete")
     */
//Request $request =  car elles manipulent la fonction
    public function cartDelete(Request $request){
     $request -> getSession() -> set('panier', []);
     $this -> addFlash('success', 'Le panier a bien été vidé');
     return $this->redirectToRoute('cart');
    }

    /**
     * @Route ("cart_item_delete/{key}", name="cart_item_delete")
     */
    public function cartItemDelete($key, Request $request){
    $panier = $request -> getSession() -> get('panier');
    array_splice($panier, $key, 1);
    $request ->getSession() -> set('panier', $panier);
        $this->addFlash('success', 'Le produit a bien été supprimé du panier');
    return $this -> redirectToRoute('cart');
    }

    /**
     * Modifier la quantité d'un produit dans le panier
     * @Route ("/cart/update-quantity/{id}", name="cart_item_update_quantity")
     */

    public function cartItemUpdateQuantity(Product $product, Request $request, SessionInterface $session )
    {
        #Recupérer quantité souhaitée
        $qt = $request->request->get('qt', 1);
        $panier = $session->get('panier');


        #rechercher le produit dans le panier
        $id_produits = array_map(function($prod){
            return $prod['product']-> getId();
        }, $panier);

        $cle_produit = array_search($product->getId(), $id_produits);


        #si la quantitié n'est pas un entier positif, on supprime le produit du panier

        if((!ctype_digit($qt)|| $qt < 1) && $cle_produit !== false){
            return $this->redirectToRoute('cart_item_delete', [
                'key' => $cle_produit
            ]);
        }


        #mettreà jour la quantité (si le produit existe dans le panier)
        if($cle_produit !== false){
        $panier[$cle_produit]['quantity']=$qt;
        $session->set('panier', $panier);
        $this->addFlash('success', sprintf(
            'La quantité du produit "%s" a été modifiée',
            $product->getTitle()
        ));
        }
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/checkout", name="cart_checkout")
     * @IsGranted("ROLE_USER")
     */
    public function cartCheckout(SessionInterface $session, StripeClient $stripe)
    {
        //Calculer le montant de la commande
        $panier = $session->get('panier', []);
        if($panier === []){
            return $this->redirectToRoute('home');
        }

        $total=0;
        foreach ($panier as $achat){
            $total += $achat['product']->getPrice() * $achat['quantity'];
        }
        return $this->render('order/checkout.html.twig',[
            'total' => $total,
        ]);
    }
}
