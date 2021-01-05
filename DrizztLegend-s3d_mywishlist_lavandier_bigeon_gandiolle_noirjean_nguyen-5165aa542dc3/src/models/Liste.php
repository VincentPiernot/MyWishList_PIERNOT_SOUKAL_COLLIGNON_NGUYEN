<?php

namespace mywishlist\models;

class Liste extends \Illuminate\Database\Eloquent\Model {
  protected $table = 'liste';
  protected $primaryKey = 'no';

  public $timestamps = false;

//Fonction qui definit les relations d'un item
  public function items(){
    return $this->hasMany('Item','no');
  }

//Fonction qui definit les relations d'un message
  public function messages(){
    return $this->hasMany('messagePublic','no');
  }

//Fonction qui definit les relations d'un utilisateur
  public function user_id() {
    return $this->belongsTo('User','idu') ;
  }

}
