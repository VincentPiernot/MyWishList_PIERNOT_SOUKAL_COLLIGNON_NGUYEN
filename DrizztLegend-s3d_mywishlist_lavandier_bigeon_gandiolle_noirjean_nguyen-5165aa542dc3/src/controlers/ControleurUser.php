<?php
namespace mywishlist\controlers;

use \mywishlist\views\VueConnexion as VueConnexion;
use \mywishlist\views\VueAccueil as VueAccueil;
use \mywishlist\models\Authentification as Authentification;
use \mywishlist\models\User as User;
use \mywishlist\views\VueUser as VueUser;

use \mywishlist\models\Liste as Liste;
use \mywishlist\models\Item as Item;
use \mywishlist\models\MessagePublic as Messagepublic;
use \mywishlist\models\Participation as Participation;

class ControleurUser {


  //Retourne les differents noms d'utilisateurs ayants cree des listes
  function createurDeListes($app,$rs) {

    $listes = Liste::where('user_id','!=',null)->where("publique","=",0)->get();

    $users = array();

    foreach($listes as $liste) {
      $user = User::where('idu','=',$liste->user_id)->first();
      if(!in_array($user,$users)) {
        $users[] = $user;
      }
    }

    $view = new VueUser();
    return $rs->getBody()->write($view->renderCreateurs($users,$app));

  }

  //Retourne l'ensemble des participations d'un utilisateur
  function mesParticipations($app,$rs) {

    if(isset($_SESSION["profile"])) {

      $user = $_SESSION["profile"];

      $participations = Participation::where("user_id","=",$user->idu)->get();
      $items = array();

      foreach($participations as $part) {
          $items[] = Item::where("id","=",$part->item_id)->first();
      }


      $listes = array();

      foreach($items as $i) {
         $listes[] = Liste::where("no","=",$i->liste_id)->first();
      }

      $view = new VueUser();
      return $rs->getBody()->write($view->renderParticipations($listes,$items,$app));

    }

    return $rs->getBody()->write("erreur");

  }

}
