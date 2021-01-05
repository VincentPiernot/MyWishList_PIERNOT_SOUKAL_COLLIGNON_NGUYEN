<?php

namespace mywishlist\views;

class VueConnexion {

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

  //Renvoie le formulaire de modification de compte
  public function renderModif($app,$erreur) {

    $url = $app->router->pathFor("route_index");
    $path = $url . "changerMdp";
    $path2 = $url . "supprimerCompte";

    $html = self::getHeader($app) . <<<END

<div id="zone">
<center>
  <h1>
    Nouveau mot de passe ?
  </h1>
<form action="$path" method="post">

<input type="password" name="mdp">

<input type="submit" value="Changer le mot de passe" </input>
</br>
</br>
<a class = "abutton" href = "$path2 " id="supp"> Supprimer votre compte </a>

$erreur

</form>
</center>
</div>
</body>
END;

    $html = $html . self::getFooter();

  return $html;

  }

  public static function getFooter() {

    return <<<END
<footer>
Eliott Gandiolle | Sacha Noirjean |  Benjamin Lavandier | Emmanuel Bigeon | Hoang Anh Nguyen
</footer>
</html>
END;
  }

  //Renvoie le formulaire de connexion ou d'authentification
  public function render($app,$type,$erreur) {

    $url = $app->router->pathFor("route_index");

    switch($type) {
        case "con":
          $envoie = $app->router->pathFor("connexion");
          $intitule = "Connexion";
          break;
        case "auth":
          $intitule = "Enregistrement";
          $envoie = $app->router->pathFor("enregistrement");
          break;
    }

    $html = self::getHeader($app) . <<<END

<div id="zone">
<center>
  <h1>
    $intitule
  </h1>
<form action="$envoie" method="post">

Login :
<input type="text" name="login">
Mot de passe : 
<input type="password" name="password">

<input type="submit" value="Valider" </input>
</br>
$erreur

</form>
</center>
</div>
</body>
END;

    $html = $html . self::getFooter();

    return $html;

  }
}
