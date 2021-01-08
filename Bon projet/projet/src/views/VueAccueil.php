<?php


namespace mywishlist\views;

class VueAccueil {


//Fonction qui genere le header de l'html de l'accueil
  public static function getHeader($app) {

    $path = $app->router->pathFor('route_index');

    return <<<END
  <!DOCTYPE html> <html>
  <head>
  <title> MyWishList </title>
  <meta charset="utf-8" />
  <link rel='icon' href="$path/web/img/icone.ico" type="image/x-icon" />
  <link type="text/css" rel="stylesheet" href="$path/css/CssGlobl.css" />
  </head>
  <body>
END;
  }

//Fonction qui genere le footer de l'html de l'accueil
  public static function getFooter() {

    return <<<END
<footer>
Collignon Alexis | Piernot Vincent | Soukal Mehdi | Hoang Anh Nguyen
</footer>
</html>
END;
  }

  //Vue en etat deconnecte
  public function renderDeco($app) {

    $url1 = $app->router->pathFor('route_index');
    $url2 = $app->router->pathFor('connexion');
    $url4 = $app->router->pathFor('enregistrement');
    $url3 = $app->router->pathFor('creerListe');
    $url5 = $app->router->pathFor('listesPubliques');
    $url6 = $app->router->pathFor('userP');

    $html = self::getHeader($app) . <<<END
<div id="menu">
<a class = "abutton" href = "$url2 "> Se connecter </a>
<a class = "abutton" href = "$url4 "> S'inscrire </a>
</div>
<center>
<div id="zone">
<h1>
  MY WISH LIST
</h1>
<a class = "abutton" href = "$url3"> Creer une nouvelle liste  </a>
<br> <a class = "abutton" href = "$url5"> Liste des listes publiques  </a> </br>
<a class = "abutton" href = "$url6"> Liste des créateurs  </a>

</div>
</center>
</body>
END;

    $html = $html . self::getFooter();

    return $html;
  }

  //Vue dans l'etat connecte
  public function renderCo($app) {

      $url1 = $app->router->pathFor('route_index');
      $url2 = $app->router->pathFor('deconnexion');
      $url3 = $app->router->pathFor('creerListe');
      $url4 = $app->router->pathFor('mesListes');
      $url5 = $app->router->pathFor('listesPubliques');
      $url6 = $app->router->pathFor('modifierCompte');
      $url7 = $app->router->pathFor('userP');
      $url8 = $app->router->pathFor('mesParticipations');

    $html = self::getHeader($app) . <<<END
<div id="menu">
<a class = "abutton" href = "$url2 "> Se deconnecter </a>
<a class = "abutton" href = "$url6"> Modifier votre compte  </a>
</div>
<center>
<div id="zone">
<h1>
  MY WISH LIST
</h1>
<br> <a class = "abutton" href = "$url3"> Creer une nouvelle liste  </a> </br>
<a class = "abutton" href = "$url4"> Acceder a vos listes  </a>
<br> <a class = "abutton" href = "$url5"> Liste des listes publiques  </a> </br>
<a class = "abutton" href = "$url7"> Liste des créateurs  </a>
<br> <a class = "abutton" href = "$url8"> Mes participations  </a> </br>

</div>
</center>
</body>
END;

    $html = $html . self::getFooter();

    return $html;
  }

}
