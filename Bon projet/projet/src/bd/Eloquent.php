<?php

namespace mywishlist\bd;

require_once('src/vendor/autoload.php');


use Illuminate\Database\Capsule\Manager as DB;


class Eloquent {

  private $db;

  public static function start($file){

    $db = new DB();
    $db->addConnection(parse_ini_file($file));
    $db->setAsGlobal();
    $db->bootEloquent();

  }
}
