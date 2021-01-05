<?php

require_once('./src/vendor/autoload.php');

\mywishlist\bd\Eloquent::start('src/conf/conf.ini');

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

session_start();

$app = new \Slim\App($configuration);

use \mywishlist\controlers\ControleurItem as ControleurItem;
use \mywishlist\controlers\ControleurListe as ControleurListe;
use \mywishlist\controlers\ControleurAccueil as ControleurAccueil;
use \mywishlist\controlers\ControleurEnregistrement as ControleurEnregistrement;
use \mywishlist\controlers\ControleurUser as ControleurUser;

$container = $app->getContainer();
$container['upload_directory'] = __DIR__ . '/web/img';

//Index
$app->get('/', function($rq, $rs){
  $cont = new ControleurAccueil();
	return $cont->afficherAccueil($rq,$rs,$this);
})->setName('route_index');

//Connection
$app->get('/enregistrement', function($rq, $rs){
  $cont = new ControleurEnregistrement();
  return $cont->afficherEnregistrement($rs,$this);
})->setName('enregistrement');

$app->post('/changerMdp', function($rq, $rs){
  $cont = new ControleurEnregistrement();
  return $cont->changerMdp($rs,$rq,$this);
})->setName('changerMdp');

$app->get('/connexion', function($rq, $rs){
  $cont = new ControleurEnregistrement();
	return $cont->afficherConnexion($rs,$this);
})->setName('connexion');

$app->get('/modifierCompte', function($rq, $rs){
  $cont = new ControleurEnregistrement();
	return $cont->modifierCompte($rs,$this);
})->setName('modifierCompte');


$app->post('/enregistrement',function($rq,$rs) {
  $cont = new ControleurEnregistrement();
	return $cont->verifierEnregistrement($rq,$rs,$this);
});

$app->post('/connexion',function($rq,$rs) {
  $cont = new ControleurEnregistrement();
	return $cont->verifierConnexion($rq,$rs,$this);
});

$app->get('/deconnexion',function($rq,$rs) {
  $cont = new ControleurEnregistrement();
	return $cont->deconnexion($rs,$this);
})->setName('deconnexion');

$app->get('/supprimerCompte',function($rq,$rs) {
  $cont = new ControleurEnregistrement();
	return $cont->supprimerCompte($rs,$this);
});

//Item

$app->get('/liste/modif/{no}/{token}/item', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->ajouterItem($this,$args["token"],$response);
});

$app->post('/item/{token}/modif/{id}/image', function($request,$response,$args){
  $cont = new ControleurItem();
  $directory = $this->get('upload_directory');
	return $cont->ajouterMonImage($this,$request,$args["token"],$args["id"],$response,$directory);
});

$app->get('/item/{token}/modif/{id}/monImage', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->formulaireMonImage($this,$args["token"],$args["id"],$response);
});

$app->post('/item/reserver/{token}/{id}', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->reserverItem($this,$args["token"],$args["id"],$request,$response);
});

$app->post('/ajoutItem/{token}', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->validationAjout($this,$args["token"],$request,$response);
});

$app->get('/item/{token}/supprimer/{id}', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->supprimerItem($this,$args["token"],$args["id"],$response);
});

$app->get('/item/{token}/modif/{id}', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->modifierItem($this,$args["token"],$args["id"],$response);
});

$app->get('/item/{token}/modif/{id}/supprimer', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->supprimerImage($this,$args["token"],$args["id"],$response);
});

$app->get('/item/{token}/modif/{id}/cagnotte0', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->creerCagnotte($this,$args["token"],$args["id"],$response,0);
});

$app->get('/item/{token}/modif/{id}/cagnotte1', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->creerCagnotte($this,$args["token"],$args["id"],$response,1);
});

$app->post('/item/{token}/modif/{id}', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->effectuerModifs($this,$args["token"],$args["id"],$response,$request);
});

$app->get('/item/afficher/{token}/{id}', function($request,$response,$args){
  $cont = new ControleurItem();
	return $cont->afficherItem($this,$args["token"],$args["id"],$response,$request);
});

//User
$app->get('/createurDeListes', function($request,$response,$args){
  $cont = new ControleurUser();
	return $cont->createurDeListes($this,$response);
})->setName('userP');

$app->get('/mesParticipations', function($request,$response,$args){
  $cont = new ControleurUser();
	return $cont->mesParticipations($this,$response);
})->setName('mesParticipations');


//Liste
$app->get('/mesListes', function($request,$response){
  $cont = new ControleurListe();
	return $cont->afficherMesListes($response,$this);
})->setName('mesListes');


$app->get('/supprimer/{token}', function($request,$response,$args){
  $cont = new ControleurListe();
	return $cont->supprimerListe($response,$this,$args["token"]);
});

$app->post('/ajouterListeModif', function($request,$response){
  $cont = new ControleurListe();
	return $cont->ajouterListeModif($request,$response,$this);
})->setName('ajouterListeModif');

$app->get('/listePubliques', function($request,$response){
  $cont = new ControleurListe();
	return $cont->afficherListesPubliques($response,$this);
})->setName('listesPubliques');

$app->get('/liste/rendrePublique/{token}', function($request,$response,$args){
  $cont = new ControleurListe();
	return $cont->changerVisibilite($response,$this,$args["token"],0);
});

$app->get('/liste/rendrePrivee/{token}', function($request,$response,$args){
  $cont = new ControleurListe();
	return $cont->changerVisibilite($response,$this,$args["token"],1);
});

$app->post('/liste/ajouterMessage/{token}', function($request,$response,$args){
  $cont = new ControleurListe();
	return $cont->ajouterMessage($request,$args["token"],$response,$this);
});

$app->post('/liste/modif/{no}/{token}', function($request,$response,$args){
  $cont = new ControleurListe();
	return $cont->effectuerModifs($args["no"],$args["token"],$request,$response,$this);
});

$app->get('/liste/modif/{no}/{token}', function($request,$response,$args){
  $cont = new ControleurListe();
	return $cont->modifierListe($args["no"],$args["token"],$response,$this);
});

$app->get('/liste/partage/{no}/{token}', function($request,$response,$args){
  $cont = new ControleurListe();
	return $cont->creerLienPartage($response,$this,$args["token"],$args["no"]);
});


$app->get('/liste/{no}/{token}', function($request,$response,$args){
  $cont = new ControleurListe();
	return $cont->afficherListe($args["no"],$args["token"],$response,$this);
});

$app->get('/creerListe', function($request,$response){
  $cont = new ControleurListe();
	return $cont->creerListe($response,$this);
})->setName('creerListe');


$app->post('/validerCreation', function($request,$response){
  $cont = new ControleurListe();
	return $cont->validerCreation($response,$request,$this);
})->setName('validerCreation');

$app->run();

?>
