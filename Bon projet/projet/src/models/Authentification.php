<?php

namespace mywishlist\models;


class Authentification {

  public static function createUser ( $username, $password ) {

    if(User::where('username','=',$username)->first() != null) {
      throw new \Exception("Un utilisateur a deja ce login");
    }

    if(strlen($password) < 7) {
      throw new \Exception("Le mot de passe doit faire au moins 8 caractères");
    }

    if (strlen($username) > 15) {
      throw new \Exception("Le login doit faire moins de 16 caractères");
    } else if (strlen($username) < 4) {
      throw new \Exception("Le login doit faire au moins 4 caractères");
    }

    $u = new User();
    $u->username = $username;
    $u->hash = password_hash($password, PASSWORD_DEFAULT,
                                   ['cost'=> 12]);
    $u->save();

    self::loadProfile($u->username);
  }

  public static function seConnecter ( $username, $password ) {

    $u = User::where('username','=',$username)->first();

    if($u == null) {
      throw new \Exception("Utilisateur inexistant");
    }

    if(password_verify($password, $u->hash)) {
      self::loadProfile($username);
    } else {
      throw new \Exception("Mauvais mot de passe");
    }

  }

  private static function loadProfile( $username ) {

    $_SESSION['profile'] = User::where('username','=',$username)->first();

  }

}

?>
