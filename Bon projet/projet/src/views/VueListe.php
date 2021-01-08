<?php

namespace mywishlist\views;

use \mywishlist\models\Item as Item;
use \mywishlist\models\Participation as Participation;

class VueListe {

//Fonction qui genere le header de l'html d'une page liste
  public static function getHeader($app) {

    $path = $app->router->pathFor('route_index');

    return <<<END
<!DOCTYPE html> <html>
<head>
<title> MyWishList </title>
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

//Fonction qui genere le footer de l'html d'une page liste
  public static function getFooter() {

    return <<<END
<footer>
Eliott Gandiolle | Sacha Noirjean |  Benjamin Lavandier | Emmanuel Bigeon | Hoang Anh Nguyen
</footer>
</html>
END;
  }

  //Affiche la page indiquant le succes de la creation du lien de partage
  public function renderSuccesPartage($app,$preUrl) {

    $path = $app->router->pathFor('route_index');
    $url  = $_SERVER['HTTP_HOST'] . $preUrl;
    $html = self::getHeader($app) . <<<END


<center>
<div id="zone">
<p> Votre lien de partage est le suivant : <input type="text" value="$url"> </p>
<a class = "abutton" href = "$path "> Retourner a l'accueil </a>
</div>
</center>

</body>
END;

    $html = $html . self::getFooter();

  return $html;
  }

  //Renvoie la liste des listes d'un utilisateur
  public function renderMesListes($listes,$app,$erreur) {

    $content = "";
    $i = 1;
    foreach($listes as $val) {
      $path = $app->router->pathFor("route_index") . "/liste/modif/$val->no/$val->tokenModif";
      $content .= "<p> $i : <a href= '$path'> $val->titre </a> </p>";
      $i++;
    }

    $path = $app->router->pathFor("ajouterListeModif");

    $html = self::getHeader($app) . <<<END
    <div id="zone">
    <h1> Vos listes : </h1>
     $content

     <form id="ajListee" method="post" action="$path">
    Vous pouvez ajouter l'une de vos listes avec son lien de modification :
      <input type="text" name="nomLis" >  <input type="submit" value="Ajouter la liste" </input>

     </form>
     $erreur
     </div>
    </body>
END;

    $html = $html . self::getFooter();

      return $html;
  }

  //Renvoie la liste des listes publiques
  public function renderListesPubliques($listes,$app) {

    $content = "";
    $i = 1;
    foreach($listes as $val) {
      $path = $app->router->pathFor("route_index") . "/liste/$val->no/$val->tokenAcces";
      $content .= "<p> $i : <a href= '$path'> $val->titre </a> </p>";
      $i++;
    }

    $html = self::getHeader($app) . <<<END
<div id="zone">
<h1> Listes publiques : </h1>
     $content
</div>
    </body>
END;

    $html = $html . self::getFooter();

      return $html;
  }


  //Renvoie l'affichage d'une liste
  public function renderListe($liste,$items,$app,$messages) {

    $titre = "<h1> Nom de la liste : $liste->titre </h1>";
    $path = $app->router->pathFor('route_index');

    $contentL = "<h2> Description : ". $liste->description . " </h2> <h2> Expire le : " . $liste->expiration . "</h2> <br>";


    $tableau = <<<END
<table> <tr> <th scope=col> Image </th> <th scope = col> Nom </th> <th scope = col> Description </th> <th scope = col> Etat de la reservation </th> </tr>
END;

    foreach($items as $item) {
       $tableau .= "<tr>";
       if($item->img != null){
         $tab1 = explode("/",$item->img);
         if($tab1[0] == "web") {
           $tableau .= "<td> <img src=$path/$item->img alt=\"pas d'images\" width= 50 height = 50 > </td>";
         } else {
           $tableau .= "<td> <img src=$item->img alt=\"pas d'images\" width= 50 height = 50 > </td>";
         }
       } else {
         $tableau .= "<td> Pas d'images </td>";
       }
       $url = $path . "item/afficher/$liste->tokenAcces/$item->id";
       $tableau .= "<td> <a href=\"$url\"> $item->nom </td>";
       $tableau .= "<td> $item->descr </td>";

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

       $date = date("Y-m-d");

       $parts = Participation::where("item_id","=",$item->id)->first();


       if($res && $liste->expiration >= $date) {

         if($parts != null) {
           $tableau .= "<td> Item réservé </td>";
         } else {
           $tableau .= "<td> Item non réservé </td>";
         }

       } else {

         $val = "Reserver";
         if($item->cagnotte != -1) {

           $val = "Participer a la cagnotte";
           if($item->cagnotte >= $item->tarif) {
             $tableau .= "<td> Cagnotte pleine ! </td>";
           } else {
             $tableau .= <<<END
       <td> <a class = "abutton" href = "$url "> $val </a> </td>
END;
           }

        } else {

          if($parts != null) {
            $tableau .= "<td> Item réservé par $parts->nomP </td>";
          } else {
            $tableau .= <<<END
      <td> <a class = "abutton" href = "$url "> $val </a> </td>
END;
          }
        }
       }
       $tableau .= "</tr>";
    }
    $tableau .= "</table>";

    $msgs = "";

    $i = 1;
    foreach($messages as $msg) {
      $msgs .= "<p> $i : $msg->message </p> </br>";
      $i++;
    }

    $path6 = $app->router->pathFor("route_index") . "liste/ajouterMessage/$liste->tokenAcces";

$html = self::getHeader($app) . <<<END

<div id="zoneListe">
 $titre
 $contentL
 <h2> Items : </h2>
 $tableau
 </br>
 <h2> Messages : </h2> </br>

 $msgs

 <form id="ajMsg" method="post" action="$path6">

 Ajouter un message ?  <input type="text" name="msg">  <input type="submit" value="Ajouter le message" </input>

 </form>
</div>
</body>
END;

    $html = $html . self::getFooter();

  return $html;
  }


  //Renvoie le formulaire de mofication d'une liste
  public function renderModifListe($liste,$items,$app,$token,$no,$erreur) {

    $url = $app->router->pathFor('route_index');
    $path = $url . "liste/modif/$no/$token";
    $path2 = $path . "/item";
    $path5 = $url . "liste/partage/$no/$token";
    $tableau = "";
    $res = true;

    if(!empty($items)){
      $tableau = '<table> <tr> <th scope="col"> Image </th> <th scope = "col">'.
      'Nom </th> <th scope = "col"> Description </th> <th scope = "col">'.
      'Modifier </th> <th scope = "col"> Supprimer </th></tr>';
      foreach($items as $item) {

        if($res) {
          $res = null == Participation::where("item_id","=",$item->id)->first();
        }

        $tableau .= "<tr>";
        if($item->img != null){
          $tab1 = explode("/",$item->img);
          if($tab1[0] == "web") {
            $tableau .= "<td> <img src=$url/$item->img alt=\"$item->descr\" width= 50 height = 50 > </td>";
          } else {
            $tableau .= "<td> <img src=$item->img alt=\"$item->descr\" width= 50 height = 50 > </td>";
          }
        } else {
          $tableau .= "<td> Pas d'images </td>";
        }
        $tableau .= "<td> $item->nom </td>";
        $tableau .= "<td> $item->descr </td>";
        $parts = Participation::where("item_id","=",$item->id)->first();

        if($parts == null){
          $path3 = $url . "item/$token/modif/$item->id";
          $path4 = $url . "item/$token/supprimer/$item->id";
          $tableau .= <<<END
<td> <a class = "abutton" href = "$path3 "> Modifier </a> </td>
<td> <a class = "abutton" href = "$path4 "  id="supp"> Supprimer </a> </td>
END;
        } else {
          $tableau .= "<td> Item réservé </td>";
          $tableau .= "<td> Item réservé </td>";
        }

        $tableau .= "</tr>";

     }
      $tableau .= "</table>";
    }

    if($liste->publique == 1) {
      $paTh = $url . "liste/rendrePublique/$liste->tokenModif";
      $boutonP = <<<END
  <a class = "abutton" href = "$paTh "> Publier la liste </a>
END;
  }  else {
      $paTh = $url . "liste/rendrePrivee/$liste->tokenModif";
      $boutonP = <<<END
  <a class = "abutton" href = "$paTh "> Rendre la liste privee </a>
END;
    }

    $path7 = $url . "supprimer/$liste->tokenModif";

    $boutonSupprimer = "";

    if($res) {
      $boutonSupprimer = <<<END
      <a class = "abutton" href = "$path7 " id="supp"> Supprimer </a>
END;
    }


    $html = self::getHeader($app) . <<<END
    <div id="zoneListe">
<center>
<form id="modifListe" method="post" action="$path">
<b>Titre</b>
	<input type="text" name="titre" value="$liste->titre">
	<br> <b>Description</b><input type="text" name="description" value="$liste->description"> </br>
	<b>Expiration</b> <input type="date" name="expiration" value="$liste->expiration">

  <input type="submit" value="Enregistrer les modifications" </input>

</form>
$erreur
$tableau
<a class = "abutton" href = "$path2 "> Ajouter un objet </a>
<a class = "abutton" href = "$path5 "> Generer lien de partage </a>
$boutonP
$boutonSupprimer
</br></br></br></br></br></br>

</center>
</div>
</body>
END;

    $html = $html . self::getFooter();

  return $html;
  }

  //Renvoie le formulaire de creation de liste
  public function renderCreerListe($app,$erreur) {

    $path = $app->router->pathFor('validerCreation');

    $html = self::getHeader($app) . <<<END


<center>
<div id="zone">
<form id="creationListe" method="post" action="$path">
	<input type="text" name="titre" placeholder="Titre">
	<br> <input type="text" name="description" placeholder="Description"> </br>
	<input type="date" name="expiration">

  <input type="submit" value="Valider" </input>

</form>

$erreur
</div>
</center>

</body>
END;

    $html = $html . self::getFooter();

    return $html;

  }

  public function succesCreation($app,$preUrl) {
    $path = $app->router->pathFor('route_index');
    $url  = $_SERVER['HTTP_HOST'] . $preUrl;
    $html = self::getHeader($app) . <<<END

<center>
<div id="zone">
<p> Votre lien de modification est le suivant : <input type="text" value="$url"> </p>
<a class = "abutton" href = "$path "> Retourner a l'accueil </a>
</div>
</center>

</body>
END;

    $html = $html . self::getFooter();

  return $html;
  }
}
