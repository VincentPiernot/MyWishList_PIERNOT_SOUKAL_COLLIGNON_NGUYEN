<?php

namespace mywishlist\views;

use \mywishlist\models\Participation as Participation;

class VueItem {

//Fonction qui genere le header de l'html d'une page item
   public static function getHeader($app) {

     $path = $app->router->pathFor('route_index');

     return <<<END
 <!DOCTYPE html> <html>
 <head>
 <title> Mon item </title>
 <meta charset="utf-8" />
 <link rel='icon' href="$path/web/img/icone.ico" type="image/x-icon" />

 <link type="text/css" rel="stylesheet" href="$path/css/CssGlobal.css" />
 </head>
 <body>
 <div id ="menu">
 <a class = "abutton" href = "$path "> Accueil </a>
 </div>
END;

   }

//Fonction qui genere le footer de l'html d'une page item
   public static function getFooter() {

     return <<<END
 <footer>
 Eliott Gandiolle | Sacha Noirjean |  Benjamin Lavandier | Emmanuel Bigeon | Hoang Anh Nguyen
 </footer>
 </html>
END;
   }


   //Renvoie le formulaire permettant l'upload d'une image
   public function renderUploadImg($app,$token,$id,$erreur) {

     $path = $app->router->pathFor('route_index') . "item/$token/modif/$id/image";

     $html = self::getHeader($app) . <<<END
     <div id="zone">
     <form method="post" action="$path"enctype="multipart/form-data">
     <h1> Fichier : </h1>
     <input type="file" name="fichier"> </br>
     </br>
     <input type="submit" value="Enregistrer cette image">
     </form>
     $erreur
     </div>

END;
   $html .= self::getFooter($app);

  return $html;
   }

   //Envoie le formulaire de moficiation d'un item
   public function renderModifItem($app,$token,$item,$erreur) {

     $path = $app->router->pathFor('route_index') . "item/$token/modif/$item->id";
     $path1 = $app->router->pathFor('route_index') . "item/$token/modif/$item->id/supprimer";
     $path2 = $app->router->pathFor('route_index') . "item/$token/modif/$item->id/monImage";
     $path3 = $app->router->pathFor('route_index') . "item/$token/modif/$item->id/cagnotte";

     $fonctionnement = "normal";
     if($item->cagnotte != -1) {
       $fonctionnement = "Cagnotte";
       $path3 .= "0";
       $cagnotte = <<<END
      <a href="$path3" class ="abutton">Enlever le fonctionnement par cagnotte </a>
END;
    } else {
      $fonctionnement = "En une participation";
      $path3 .= "1";
      $cagnotte = <<<END
      <a href="$path3" class ="abutton">  Faire fonctionner par cagnotte </a>
END;
    }

     $html = self::getHeader($app) . <<<END

     <div id="zoneItem">
     <form id="ajoutItem" method="post" action="$path">
     <h2> [Fonctionnement : $fonctionnement] </h2>
     Nom :
     <input type="text" name="nom" value = "$item->nom">
     <br> Description : <input type="text" name="description" value = "$item->descr"> </br>
     Prix : <input type="number" name="prix" value = "$item->tarif"> € </br>
     (Facultatif) Url decrivant le produit : <input type="text" name="url" value="$item->url"> </br>
     (Facultatif) Url d'une image : (soit commencant par http soit par web/img) <input type="text" name="img" value="$item->img">

     <input type="submit" value="Valider les modifications" </input>

     </form>      </br> <a href="$path2" class ="abutton"> Upload une image </a>
$cagnotte
 <a class = "abutton" href = "$path1 "  id="supp"> Supprimer l'image </a>

     $erreur

     </div>
     </body>
END;

    $html = $html . self::getFooter();

     return $html;
   }

   //Envoie le formulaire de creation d'un item
   public function renderCreation($app,$token,$erreur) {

     $path = $app->router->pathFor('route_index') . "ajoutItem/" . $token;

     $html = self::getHeader($app) . <<<END

<div id="zone">
<form id="ajoutItem" method="post" action="$path">
Nom :
<input type="text" name="nom">
<br> Description : <input type="text" name="description"> </br>
Prix : <input type="number" name="prix" min="0"> € </br>
(Facultatif) Url decrivant le produit : <input type="text" name="url"> </br>
(Facultatif) Url d'une image : (soit commencant par http soit par web/img)<input type="text" name="img">

<input type="submit" value="Ajouter l'item" </input>

</form>
$erreur
</div>
</body>
END;

    $html = $html . self::getFooter();

    return $html;

   }

   //Envoie la page d'un item
   public function renderItem($app,$item,$liste,$erreur){

     $path = $app->router->pathFor('route_index');

     $content = "<h1> Nom : $item->nom </br> Description : $item->descr </br> </h1> <h2> Tarif : $item->tarif € </h2>";
     if($item->img != null){
       $tab1 = explode("/",$item->img);
       if($tab1[0] == "web") {
         $image = " <img src=$path/$item->img alt=\"Image non chargee\" width= 300 height = 300 >";
       } else {
         $image = " <img src=$item->img alt=\"Image non chargee\" width= 300 height = 300 >";
       }
     } else {
       $image = "<p> Pas d'images pour cet item ! </p>";
     }



     $res = false;

     if(isset($_SESSION["profile"])) {
       if($_SESSION["profile"]->idu == $liste->user_id) {
         $res=true;
       }
     } else {
       if(isset($_COOKIE["createur"])) {
          $tab = explode(";",$_COOKIE["createur"]);
          if(in_array($liste->no,$tab)) {
             $res = true;
          }
       }
     }

     $url = $app->router->pathFor("route_index") . "item/reserver/$liste->tokenAcces/$item->id";

     $date = date("Y-m-d");

     $parts = Participation::where("item_id","=",$item->id)->first();


     $form = "";
     $cagnotte = "";
     $ligneForm = "";
     if($item->cagnotte != -1) {
        $cagnotte = "Deja " . $item->cagnotte . "€ dans la cagnotte !";
        $reste = $item->tarif - $item->cagnotte;
        $ligneForm = <<<END
Montant a ajouter a la cagnotte : <input type="number" name="montant" min="1" max="$reste"> </br>;
END;
     }

     if($res && $liste->expiration >= $date) {

       if($parts != null) {
         $form = "<p> Des personnes ont participees a la cagnotte </p>";
       } else {
         $form = "<p> Aucun participant </p>";
       }

     } else {

       if($item->cagnotte != -1) {

         $boolean = true;
         if(isset($_SESSION["profile"])) {
           $boolean = null == Participation::where("item_id","=",$item->id)->where("nomP","=",$_SESSION["profile"]->username)->first();
         }

         if($item->cagnotte < $item->tarif && $boolean) {

             $nom = "";
             if(isset($_SESSION["profile"])) {
               $nom = $_SESSION["profile"]->username;
             }
             $form = <<<END
             <div id="message">
             <form id="ajReserv" method="post" action="$url">
             Nom : <input type="text" name="nom" value = "$nom"> </br>
             $ligneForm
             Rentrer un message ? (facultatif) <input type="text" name="message">

             <input type="submit" value="Participer" </input>
             </form>
             $erreur
             </div>
END;
         } else {

           $parts = Participation::where("item_id","=",$item->id)->get();
           $form .= "<p> Ces personnes ont participees a la cagnotte : <p>";
           foreach($parts as $p) {
             $form .= " <p> $p->nomP a ajoute $p->montant € a la cagnotte </p>";
             if($p->message != null) {
               $form .= "<p> Message : $p->message </p>";
             }
             $form .= "</br> ";
           }

        }

      } else {

        if($parts != null) {

          $form .= "<p> $parts->nomP a reserve l'item </p>";
          if($parts->message != null) {
            $form .= "<p> Message : $parts->message </p>";
          }

        } else {

          $nom = "";
          if(isset($_SESSION["profile"])) {
            $nom = $_SESSION["profile"]->username;
          }
          $form = <<<END
          <div id="message">
          <form id="ajReserv" method="post" action="$url">
          Nom : <input type="text" name="nom" value = "$nom"> </br>
          $ligneForm
          Rentrer un message ? (facultatif) <input type="text" name="message">

          <input type="submit" value="Participer" </input>
          </form>
          $erreur
          </div>
END;

        }
      }
    }

     $html = self::getHeader($app) . <<<END
<div id="image">
$image
</div>
<div id ="texteItem">

$content
<h2> $cagnotte </h2>
$form
</div>


</body>
END;

    $html = $html . self::getFooter();

     return $html;
   }

}
