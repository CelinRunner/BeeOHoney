<?php

namespace App\Controller;

use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
//("/incription", name="inscription") ->  user/register.html.twig
//'Je suis dans la page d'inscription'
//
//        ("/connexion", name="connexion") -> user/login.html.twig
//        'Je suis dans la page de connexion'
//
//        ("/deconnexion", name="deconnexion") --- fonction vide ----
//        ("/connexion_check"; name="connexion_check") --- fonction vide ----

    /**
     * @Route ("/inscription", name="register")
     */
//   pk getmanager(dans doctrine) fonction persist et flush , c est le manager qui l'a et non pas le repository'
    public function register(Request $request, UserPasswordEncoderInterface $encoder){
        $manager = $this -> getDoctrine() -> getManager();
        $user = new User;
        $form = $this -> createForm(UserType::class, $user);

        $form ->handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            $user -> setRole('ROLE_USER');
            $user -> setRegisterDate(new \DateTime('now'));

            $password = $user -> getPassword();
//            $user -> setPassword($encoder -> encodePassword($user, $password)); vaut les deux lignes suivantes =
            $password_hash = $encoder -> encodePassword($user, $password);
            $user -> setPassword($password_hash);

            $manager -> persist($user);
            $manager -> flush();

            $e = ($user -> getSexe() == 'm') ? '' : 'e';
            $this -> addFlash('success', 'Félicitation, vous êtes bien inscrit' . $e);
//            $this -> addFlash('success', 'Félicitation, votre inscription a bien été prise en compte');
            return $this -> redirectToRoute('login');
        }

        return $this->render('user/register.html.twig',[
            'userForm' => $form -> createView()
        ]);
    }

    /**
     * @Route ("/connexion", name="login")
     */
    public function login(AuthenticationUtils $auth){

        $lastUsername = $auth -> getLastUsername();
        $error = $auth -> getLastAuthenticationError();

        if($error){
            $this -> addFlash('errors', 'Problème d\'identifiant');
        }

        return $this->render('user/login.html.twig', [
            'lastUsername' => $lastUsername
        ]);
    }

    /**
     * @Route ("/connexion_check", name="login_check")
     */
    public function longinCheck(){

    }

    /**
     * @Route ("/deconnexion", name="logout")
     */
    public function logout(){

    }

    /**
     * @Route ("/test-email", name="test_email")
     *
     */
    public function testEmail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('johndoe@exemple.fr')
            ->to ('toto@exemple.fr')
            ->subject('Test Mailer Symfony')
            ->text('Envoyé depuis Symfony');

        $mailer->send($email);
        dd('Mail envoyé !');
    }

}
