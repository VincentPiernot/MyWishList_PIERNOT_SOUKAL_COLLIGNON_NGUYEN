<?php

namespace mywishlist\controlers;

use \mywishlist\views\VueItem as VueItem;
use \mywishlist\views\VueListe as VueListe;

use \mywishlist\models\Item as Item;
use \mywishlist\models\Liste as Liste;
use \mywishlist\models\Participation as Participation;

class ControleurItem {

    //Renvoie la page qui affiche un item
    function afficherItem($app,$token,$id,$rs) {
      $liste = Liste::where('tokenAcces','=',$token)->first();

      if($liste != null){
        $item = Item::where('id', '=', $id)->first();
    	  $view = new VueItem();
        return $rs->getBody()->write($view->renderItem($app,$item,$liste,""));
      }

     return $rs->getBody()->write("Vous essayez d'acceder a un item inexistant");

   }

   //Ajoute une image via un upload
   function ajouterMonImage($app,$rq,$token,$id,$rs,$dir) {
     if(null != Liste::where("tokenModif","=",$token)->first()){

        $erreur = "";
         $item = Item::where("id","=",$id)->first();
         $data = $rq->getUploadedFiles();

         $image = $data["fichier"];



         $type = $image->getClientMediaType();

         $nom = filter_var($image->getClientFilename(),FILTER_SANITIZE_URL);

         if ((($type == "image/gif")
         || ($type == "image/jpeg")
         || ($type == "image/pjpeg")
         || ($type == "image/jpg")
         || ($type == "image/png"))
         && ($image->getSize() < 5000000)
         && (strlen($nom) < 45)){

           if ($image->getError() !== UPLOAD_ERR_OK){
               $erreur = "Erreur lors de l'upload";
           } else {

              if(!file_exists($dir . "/" . $nom)) {
                 $image->moveTo($dir . "/" . $nom);
                 $item->img = "web/img/". $nom;
                 $item->save();
                 return $rs->withRedirect($app->router->pathFor("route_index") . "item/$token/modif/$id");
              } else {
                 $erreur = "Une image de ce nom existe deja";
              }
           }

        } else {
          $erreur = "Fichier incorrect ou trop volumineux";

        }

     }

     $view = new VueItem();
     return $rs->getBody()->write($view->renderUploadImg($app,$token,$id,$erreur));

   }


   //Renvoie le formulaire permettant d'uploader une image
    function formulaireMonImage($app,$token,$id,$rs) {
      if(null != Liste::where("tokenModif","=",$token)->first()) {
        $view = new VueItem();
        return $rs->getBody()->write($view->renderUploadImg($app,$token,$id,""));
      }
      return $rs->getBody()->write("erreur");

    }

    //Permet de reserver un item
    function reserverItem($app,$token,$id,$rq,$rs) {
      $data = $rq->getParsedBody();
      $msg1 = "";
      if(isset($data["message"])){
        $msg1 = $data["message"];
      }
      $item = Item::where("id","=",$id)->first();

      $montant = $item->tarif;
      $nom = filter_var($data["nom"],FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
      $msg = filter_var($msg1,FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);


      if(isset($data["montant"]) && $data["montant"] != "") {
        $montant = $data["montant"];
      } else {
        if($item->cagnotte != -1) {
          $montant = -2;
        }
      }

      if($nom != $data["nom"] || $msg1 != $msg) {
        $erreur = "Caracteres non valides";
      } else {
        if($nom != "" && $montant != -2) {
          $parti = Participation::where("nomP","=",$nom)->where("item_id","=",$id)->first();
          if($parti == null) {
            $participation = new Participation();
            $participation->item_id = $id;
            $participation->nomP = $nom;

            if(isset($_SESSION["profile"])){
              $participation->user_id = $_SESSION["profile"]->idu;
            }
            if($msg != "") {
              $participation->message = $msg;
            }

            $participation->montant = $montant;
            if($item->cagnotte != -1) {
              $item->cagnotte += $montant;
              $item->save();
            }
            $participation->save();

            $view = new VueItem();
            return $rs->getBody()->write($view->renderItem($app,$item,Liste::where("tokenAcces","=",$token)->first(),""));
          } else {
            $erreur = "Une personne de ce nom a deja participe";
          }

        } else {
          $erreur = "Veuillez remplir les champs obligatoires";
        }
      }

      $view = new VueItem();
      return $rs->getBody()->write($view->renderItem($app,$item,Liste::where("tokenAcces","=",$token)->first(),$erreur));

    }

    //Renvoie le formulaire permettant d ajouter un item a une liste
    function ajouterItem($app,$tokenListe,$rs){
      $view = new VueItem();
      return $rs->getBody()->write($view->renderCreation($app,$tokenListe,""));
    }

    //Supprime un item
    function supprimerItem($app,$token,$id,$rs) {
      $liste = Liste::where('tokenModif','=',$token)->first();

      if($liste != null){
        $item = Item::where('id','=',$id)->first();
        $item->delete();
        return $rs->withRedirect($app->router->pathFor('route_index') . "liste/modif/" . $liste->no . "/" . $token);
     }

      return $rs->getBody()->write("Vous essayez d'acceder a une liste inexistante");

    }

    //Permet de passer un item en mode cagnotte et aussi d'enlever ce mode
    function creerCagnotte($app,$token,$id,$rs,$type) {
      $liste = Liste::where('tokenModif','=',$token)->first();

      if($liste != null){
        $item = Item::where('id','=',$id)->first();
        if($type == 1) {
          $item->cagnotte = 0;
        } else {
          $item->cagnotte = -1;
        }
        $item->save();
        return $rs->withRedirect($app->router->pathFor('route_index') . "item/$token/modif/$id" );
     }

      return $rs->getBody()->write("Vous essayez d'acceder a une liste inexistante");

    }

    //Renvoie la page de modification d'un item
    function modifierItem($app,$token,$id,$rs) {
      if(null != Liste::where('tokenModif','=',$token)->first() && null != Item::where('id','=',$id)){
        $view = new VueItem();
        $item = Item::where('id', '=',$id)->first();
        return $rs->getBody()->write($view->renderModifItem($app,$token,$item,""));
      }
    }

    //Supprime une image attribuee a un item
    function supprimerImage($app,$token,$id,$rs) {
      if(null != Liste::where('tokenModif','=',$token)->first() && null != Item::where('id','=',$id)->first()){
        $view = new VueItem();
        $item = Item::where('id', '=',$id)->first();
        $item->img = null;
        $item->save();
        return $rs->getBody()->write($view->renderModifItem($app,$token,$item,""));
      }
    }

    //Teste et execute les modifications faites sur un item
    function effectuerModifs($app,$token,$id,$rs,$rq) {

      if(null != Liste::where('tokenModif','=',$token)->first() && null != Item::where('id','=',$id)->first()){
        $item = Item::where('id','=',$id)->first();
        if($item == null) {
          return;
        }
        $liste = Liste::where('tokenModif', '=', $token)->first();
        $data = $rq->getParsedBody();

        $nom1 = $data["nom"];
        $descr1 = $data["description"];
        $prix = $data["prix"];
        $url1 = "";
        $img1 = "";
        if(isset($data["url"])){
          $url1 = $data["url"];
        }
        if(isset($data["img"])){
          $img1 = $data["img"];
        }
        $erreur = "";

        $nom = filter_var($nom1,FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
        $descr = filter_var($descr1,FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
        $url = filter_var($url1,FILTER_SANITIZE_URL);
        $img = filter_var($img1,FILTER_SANITIZE_URL);

        if(!($descr != $descr1 || $nom != $nom1 || $url1 != $url || $img != $img1)) {
            if($nom != "" && $descr != "" && $prix != "") {
              $item->nom = $nom;
              $item->descr = $descr;
              $item->tarif = $prix;
              if(isset($data["url"])) {
                $item->url = $url;
              }
              if(isset($data["img"])) {
                $item->img = $img;
              }
              $item->save();
              $url = $app->router->pathFor('route_index') . "item/$token/modif/$item->id";
              return $rs->withRedirect($url);
            } else {
              $erreur = "Certains champs sont encore vides";
            }

        } else {
          $erreur = "Veuillez supprimer tout caractère spécial / balise
          html";
        }

        $view = new VueItem();
        return $rs->getBody()->write($view->renderModifItem($app,$token,$item,$erreur));
      }
    }


    //Teste et ajoute un item dans une liste
    function validationAjout($app,$token,$rq,$rs) {
      $data = $rq->getParsedBody();
      $nom1 = $data["nom"];
      $descr1 = $data["description"];
      $prix = $data["prix"];
      $url1 = "";
      $img1 = "";
      if(isset($data["url"])){
        $url1 = $data["url"];
      }
      if(isset($data["img"])){
        $img1 = $data["img"];
      }
      $erreur = "";

      $nom = filter_var($nom1,FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
      $descr = filter_var($descr1,FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);
      $url = filter_var($url1,FILTER_SANITIZE_URL);
      $img = filter_var($img1,FILTER_SANITIZE_URL);


      if(!($descr != $descr1 || $nom != $nom1 || $url1 != $url || $img != $img1)) {
          if($nom != "" && $descr != "" && $prix != "") {
            $item = new Item();
            $item->nom = $nom;
            $item->descr = $descr;
            $item->tarif = $prix;
            if(isset($data["url"])) {
              $item->url = $url;
            }
            if(isset($data["img"])) {
              $item->img = $img;
            }
            $liste = Liste::where('tokenModif','=',$token)->first();
            $no = $liste->no;
            $item->liste_id = $no;
            $item->save();
            $url = $app->router->pathFor('route_index') . "liste/modif/" . $no . "/" . $token;
            return $rs->withRedirect($url);
          } else {
            $erreur = "Certains champs sont encore vides";
          }

      } else {
        $erreur = "Veuillez supprimer tout caractère spécial / balise
        html";
      }

      $view = new VueItem();
      return $rs->getBody()->write($view->renderCreation($app,$token,$erreur));
    }




}
