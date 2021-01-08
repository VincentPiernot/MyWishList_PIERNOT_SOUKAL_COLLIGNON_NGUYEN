<?php

namespace mywishlist\views;

class VueUser {

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

  public static function getFooter() {

    return <<<END
<footer>
Eliott Gandiolle | Sacha Noirjean |  Benjamin Lavandier | Emmanuel Bigeon | Hoang Anh Nguyen
</footer>
</html>
END;
  }

  //renvoie la liste des createurs
  public function renderCreateurs($users,$app) {

    $content = "";
    $i = 1;
    foreach($users as $u) {
      $content .= "<p> $i : $u->username </p>";
      $i++;
    }

    $html = self::getHeader($app) . <<<END
<div id="zone">
<h1> Pseudonymes des utilisateurs ayants au moins une liste publique : </h1>
$content
</div>
    </body>
END;

    $html = $html . self::getFooter();

      return $html;
  }

  //Renvoie la liste des participations d'un utilisateur
  public function renderParticipations($listes,$items,$app) {
    $content = "";

    if($items != null) {
      $content = "<table> <tr> <th scope=col> Indice </th> <th scope=col> Liste </th> <th scope=col> Item </th> </tr>";
    }

    $i = 0;
    foreach($items as $item) {
      $liste = $listes[$i];
      $path1 = $app->router->pathFor("route_index") . "liste/$liste->no/$liste->tokenAcces";
      $path2 = $app->router->pathFor("route_index") . "item/afficher/$liste->tokenAcces/$item->id";

      $j = $i + 1;
      $content .= "<tr> <td> $j </td>";
      $nom = $liste->titre;
      $content .= "<td> <a href=$path1> $nom </a> </td>";
      $content .= "<td> <a href=$path2> $item->nom </a> </td> </tr>";

      $i++;
    }
    if($items != null) {
      $content .= "</table>";
    }

    $html = self::getHeader($app) . <<<END
<div id="zone">
<h1> Vos participations (enregistrées lorsque vous êtes connectés) : </h1>
$content
</div>
    </body>
END;

    $html = $html . self::getFooter();

      return $html;
  }

}
