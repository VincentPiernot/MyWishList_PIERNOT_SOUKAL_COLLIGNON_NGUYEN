<?php

namespace mywishlist\controlers;
use \mywishlist\views\VueListe as VueListe;
use \mywishlist\models\Liste as Liste;
use \mywishlist\models\Item as Item;
use \mywishlist\models\MessagePublic as Messagepublic;

class ControleurListe {

    //Permet d'afficher une liste publiquement
    function afficherListe($no,$token,$rs,$app) {
      $liste = Liste::where('tokenAcces','=',$token)->first();

    	$view = new VueListe();
      $items = Item::where('liste_id', '=',$no)->get();
      if($liste != null) {
        $messages = Messagepublic::where("liste_id","=",$no)->get();
        return $rs->getBody()->write($view->renderListe($liste,$items,$app,$messages));
      } else {
        return $rs->getBody()->write("erreur");
      }
    }

    //Permet d'ajouter un message a une liste
    function ajouterMessage($request,$token,$rs,$app) {

      $liste = Liste::where('tokenAcces','=',$token)->first();

      $view = new VueListe();

      if($liste != null) {

        $messages = Messagepublic::where("liste_id","=",$liste->no)->get();
        $items = Item::where('liste_id', '=',$liste->no)->get();

              $data = $request->getParsedBody();

              $msg1 = $data["msg"];
              $msg = filter_var($msg1,FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);

              if($msg != "") {
                      $messagepublic= new Messagepublic();
                      $messagepublic->liste_id = $liste->no;
                      $messagepublic->message = $msg;
                      if(isset($_SESSION["profile"])) {
                        $messagepublic->user_id = $_SESSION["profile"]->idu;
                      }
                      $messagepublic->save();
                      $messages = Messagepublic::where("liste_id","=",$liste->no)->get();

                      return $rs->getBody()->write($view->renderListe($liste,$items,$app,$messages));

              }

              return $rs->getBody()->write($view->renderListe($liste,$items,$app,$messages));

      } else {

        return $rs->getBody()->write("erreur");

      }
    }

    //Affiche le lien de partage d'une liste
    function creerLienPartage($rs,$app,$token1,$no) {

      $liste = Liste::where('tokenModif','=',$token1)->first();
      if($liste != null) {
        $url = $app->router->pathFor('route_index') . "liste/" . $liste->no . "/" . $liste->tokenAcces;
        $view = new VueListe();
        return $rs->getBody()->write($view->renderSuccesPartage($app,$url));
      } else {
        return $rs->getBody()->write("erreur");
      }
    }

    //Permet de supprimer une liste
    function supprimerListe($rs,$app,$token) {

      $liste = Liste::where("tokenModif","=",$token)->first();

      if($liste != null) {

        foreach(Item::where("liste_id","=",$liste->no)->get() as $item) {
          $item->delete();
        }

        foreach(Messagepublic::where("liste_id","=",$liste->no)->get() as $msg) {
          $msg->delete();
        }

        $liste->delete();

        return $rs->withRedirect($app->router->pathFor("mesListes"));

      }

    }

    //Permet a un utilisateur d'ajouter une liste a son nom avec le lien de modif
    function ajouterListeModif($rq,$rs,$app) {

      $data = $rq->getParsedBody();
      if(isset($data["nomLis"])) {
        $url = filter_var($data["nomLis"],FILTER_SANITIZE_URL);
        $tab = explode("/",$url);
        $token = end($tab);

        $liste = Liste::where("tokenModif","=",$token)->first();
        if($liste != null) {


          if(isset($_SESSION["profile"])){
            $liste->user_id = $_SESSION["profile"]->idu;
            $liste->save();
            $url = $app->router->pathFor("mesListes");
            return $rs->withRedirect($url);
          }

        }
      }
      $listes = Liste::where('user_id','=',$_SESSION['profile']->idu)->get();

      $view = new VueListe();
      return $rs->getBody()->write($view->renderMesListes($listes,$app,"Lien incorrect"));

    }

    //Permet a l'utilisateur d'afficher ses listes
    function afficherMesListes($rs,$app) {

      $listes = Liste::where('user_id','=',$_SESSION['profile']->idu)->get();
      $view = new VueListe();

      return $rs->getBody()->write($view->renderMesListes($listes,$app,""));
    }

    //Retourne l'ensemble des listes publiques
    function afficherListesPubliques($rs,$app) {
      $lists = Liste::where('publique','=',0)->orderBy('expiration', 'ASC')->get();

      $listes = array();

      foreach($lists as $liste) {
        $date = date("Y-m-d");
        if($date < $liste->expiration) {
          $listes[] = $liste;
        }
      }
      $view = new VueListe();
      return $rs->getBody()->write($view->renderListesPubliques($listes,$app));
    }

    //Retourne le formulaire de creation de liste
    function creerListe($rs,$app){
      $view = new VueListe();
      return $rs->getBody()->write($view->renderCreerListe($app,""));
    }

    //Retourne le formulaire de modification de liste
    function modifierListe($no,$token,$rs,$app) {
      if(null != Liste::where('tokenModif','=',$token)->first()){
        $view = new VueListe();
        $liste = Liste::where('no', '=', $no)->first();
        $items = Item::where('liste_id', '=',$liste->no)->get();
        return $rs->getBody()->write($view->renderModifListe($liste,$items,$app,$token,$no,""));
      }
    }

    //Permet de changer la visibilite d'une liste de privee a publique et inversement
    function changerVisibilite($rs,$app,$token,$visi) {
      if(null != Liste::where('tokenModif','=',$token)->first()){
        $view = new VueListe();
        $liste = Liste::where('tokenModif','=',$token)->first();
        $liste->publique = $visi;
        $liste->save();
        $items = Item::where('liste_id', '=',$liste->no)->get();
        return $rs->getBody()->write($view->renderModifListe($liste,$items,$app,$token,$liste->no,""));
      }
    }

    //Permet de verifier et d'enregistrer les modifications faites sur une liste
    function effectuerModifs($no,$token,$rq,$rs,$app) {
      if(null != Liste::where('tokenModif','=',$token)->first()){
        $view = new VueListe();
        $liste = Liste::where('no', '=', $no)->first();
        $data = $rq->getParsedBody();

        $titre1 = $data["titre"];
        $descr1 = $data["description"];
        $date = $data["expiration"];
        $erreur = "";

        $titre = filter_var($titre1,FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
        $descr = filter_var($descr1,FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
        $items = Item::where('liste_id', '=',$liste->no)->get();

        if(!($descr != $descr1 || $titre != $titre1)) {
            if($titre != "" && $descr != "" && isset($date)) {
              $datee = date("Y-m-d");
              if( $date >= $datee) {

                $liste->titre = $titre;
                $liste->description = $descr;
                $liste->expiration = $date;
                $liste->save();
                $url = $app->router->pathFor('route_index') . "liste/modif/" . $no . "/" . $token;
                return $rs->withRedirect($url);

              } else {
                $erreur = "Date inférieure à la date actuelle";
              }
            } else {
              $erreur = "Certains champs sont vides";
            }
        } else {
          $erreur = "Veuillez supprimer tout caractère spécial / balise
          html";
        }

        return $rs->getBody()->write($view->renderModifListe($liste,$items,$app,$token,$no,$erreur));
      }
    }

    //Permet de verifier les valeurs entree dans la creation et de creer une liste
    function validerCreation($rs,$rq,$app) {
        $data = $rq->getParsedBody();
        $titre1 = $data["titre"];
        $descr1 = $data["description"];
        $date = $data["expiration"];
        $erreur = "";

        $titre = filter_var($titre1,FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
        $descr = filter_var($descr1,FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);


        if(!($descr != $descr1 || $titre != $titre1)) {
            if($titre != "" && $descr != "" && isset($date)) {
              $datee = date("Y-m-d");
              if( $date >= $datee) {
                $token = bin2hex(random_bytes(8));
                $liste = new Liste();

                if(isset($_SESSION["profile"])){
                  $liste->user_id = $_SESSION["profile"]->idu;
                }
                $liste->tokenAcces = bin2hex(random_bytes(8));
                $liste->titre = $titre;
                $liste->publique = 1;
                $liste->description = $descr;
                $liste->expiration = $date;
                $liste->tokenModif = $token;
                $liste->save();
                $liste = Liste::where('tokenModif','=',$token)->first();
                if(isset($_COOKIE["createur"])) {
                  $_COOKIE["createur"] .= ";$liste->no";
                } else {
                  setcookie("createur",$liste->no,time() + 60*60*24);
                }
                setcookie("createur",'exampleUserName',time()+31556926 ,'/');
                $url = $app->router->pathFor('route_index') . "liste/modif/" . $liste->no . "/" . $token;
                $view = new VueListe();
                return $rs->getBody()->write($view->succesCreation($app, $url));
              } else {
                $erreur = "Date inférieure à la date actuelle";
              }
            } else {
              $erreur = "Certains champs sont encore vides";
            }

        } else {
          $erreur = "Veuillez supprimer tout caractère spécial / balise
          html";
        }

        $token = base_convert(hash('sha256', time() . mt_rand()), 16, 36);


        $view = new VueListe();
        return $rs->getBody()->write($view->renderCreerListe($app, $erreur));
    }
}
