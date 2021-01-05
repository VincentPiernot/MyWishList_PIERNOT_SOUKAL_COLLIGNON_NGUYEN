<?php

namespace mywishlist\controlers;
use \mywishlist\views\VueAccueil as VueAccueil;

class ControleurAccueil {


    //Affiche la page d'accueil
    function afficherAccueil($rq,$rs,$app) {

    	$view = new VueAccueil();

      if(isset($_SESSION["profile"])) {
        return $rs->getBody()->write($view->renderCo($app));
      } else {
        return $rs->getBody()->write($view->renderDeco($app));
      }
    }
}
