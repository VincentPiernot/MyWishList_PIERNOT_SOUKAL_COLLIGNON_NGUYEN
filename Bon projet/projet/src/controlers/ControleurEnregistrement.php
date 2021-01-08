<?php
namespace mywishlist\controlers;

use \mywishlist\views\VueConnexion as VueConnexion;
use \mywishlist\views\VueAccueil as VueAccueil;
use \mywishlist\models\Authentification as Authentification;
use \mywishlist\models\User as User;
use \mywishlist\models\Liste as Liste;
use \mywishlist\models\Item as Item;
use \mywishlist\models\MessagePublic as Messagepublic;
use \mywishlist\models\Participation as Participation;
use \Exception;

class ControleurEnregistrement {


  //Affiche la page de connexion
  function afficherConnexion($rs,$app) {

    $view = new VueConnexion();
    return $rs->getBody()->write($view->render($app,"con",""));

  }

  //Affiche la page d'inscription
  function afficherEnregistrement($rs,$app) {

    $view = new VueConnexion();
    return $rs->getBody()->write($view->render($app,"auth",""));
  }

  //Verifie l'inscription
  function verifierEnregistrement($rq,$rs,$app) {
      $data = $rq->getParsedBody();
      if(isset($data["password"]) && isset($data["login"]) && !empty($data["password"]) && !empty($data["login"])) {

        $login = filter_var($data["login"],FILTER_SANITIZE_STRING);
        $password = $data["password"];

        if($login != $data["login"]) {

          $view = new VueConnexion();
          return $rs->getBody()->write($view->render($app,"auth","Le login contient des caracteres non autorisés
          (chiffres et lettres majuscules/minuscules avec ou sans accent)"));

        }

        try {
          Authentification::createUser($login,$password);
          $url = $app->router->pathFor('route_index');
          return $rs->withRedirect($url);
        } catch (\Exception $e){
          $view = new VueConnexion();
          return $rs->getBody()->write($view->render($app,"auth",$e->getMessage()));
        }
      } elseif (empty($data["password"] || empty($data["login"]))) {

          $view = new VueConnexion();
          return $rs->getBody()->write($view->render($app,"auth","Login ou mot de passe vide"));

      }

      return $rs->withRedirect($app->router->pathFor("enregistrement"));

  }

  //Permet de changer de mot de passe
  function changerMdp($rs,$rq,$app) {

    $data = $rq->getParsedBody();
    $mdp = $data["mdp"];
    $view = new VueConnexion();

    if(strlen($mdp) >= 6) {
      $profil = User::where("idu","=",$_SESSION["profile"]->idu)->first();
      $profil->hash = password_hash($mdp, PASSWORD_DEFAULT,
                                       ['cost'=> 12]);;
      $profil->save();
      unset($_SESSION["profile"]);
      return $rs->withRedirect($app->router->pathFor("connexion"));
    } else {
      $erreur = "Login vide ou incorrect";
      return $rs->getBody()->write($view->renderModif($app,$erreur));
    }
  }

  //Permet de supprimer son compte
  function supprimerCompte($rs,$app) {

    $user = User::where("idu","=",$_SESSION["profile"]->idu)->first();

    $listes = Liste::where("user_id","=",$user->idu)->get();


    foreach($listes as $liste) {
      if($liste != null) {

        foreach(Item::where("liste_id","=",$liste->no)->get() as $item) {
          $item->delete();
        }

        foreach(Messagepublic::where("liste_id","=",$liste->no)->get() as $msg) {
          $msg->delete();
        }

        $liste->delete();

        }
    }

    $participations = Participation::where("user_id","=",$user->idu)->get();
    foreach ($participations as $part) {

      $item = Item::where("id","=",$part->item_id)->first();
      $liste = Liste::where("no","=",$item->liste_id)->first();
       $date = date("Y-m-d");
       if($liste->expiration >= $date) {

         if($item->cagnotte != -1) {
            $item->cagnotte -= $part->montant;
            $item->save();
         }
         $part->delete($item->id,$user->username);
       }

    }

    $messages = Messagepublic::where("user_id","=",$user->idu)->get();

    foreach($messages as $m) {
      $m->delete();
    }

    unset($_SESSION["profile"]);
    $user->delete();
    return $rs->withRedirect($app->router->pathFor("route_index"));

  }

  //Deconnecte l utilisateur
  function deconnexion($rs,$app) {
    unset($_SESSION["profile"]);
    return $rs->withRedirect($app->router->pathFor("route_index"));
  }

  //Retourne la page permettant de modifier son compte
  function modifierCompte($rs,$app) {

    $view = new VueConnexion();
    if(isset($_SESSION["profile"])) {
      return $rs->getBody()->write($view->renderModif($app,""));
    }
  }

  //Verifie la connexion d'un utilisateur
  function verifierConnexion($rq,$rs,$app) {
      $data = $rq->getParsedBody();
      if(isset($data["password"]) && isset($data["login"]) && !empty($data["password"]) && !empty($data["login"])) {

          $login = filter_var($data["login"],FILTER_SANITIZE_STRING);

          if($login != $data["login"]) {

            $view = new VueConnexion();
            return $rs->getBody()->write($view->render($app,"con","Le login contient des caracteres non autorisés
            (chiffres et lettres majuscules/minuscules avec ou sans accent)"));

          }

          $password = $data["password"];

          try {
            Authentification::seConnecter($login,$password);
            $url = $app->router->pathFor('route_index');
            return $rs->withRedirect($url);
          } catch (\Exception $e){
            $view = new VueConnexion();
            return $rs->getBody()->write($view->render($app,"con",$e->getMessage()));
          }


      } elseif ($data["password"] == "" || $data["login"] == "") {

          $view = new VueConnexion();
          return $rs->getBody()->write($view->render($app,"con","Champs vides"));

      } else {
        $url = $app->router->pathFor('connexion');
        return $rs->withRedirect($url);
      }
  }

}
