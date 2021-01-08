<?php
namespace mywishlist\models;

class User extends \Illuminate\Database\Eloquent\Model {
  protected $table = 'user';
  protected $primaryKey = 'idu';

  public $timestamps = false;

  public function listes(){
    return $this->hasMany('Liste','idu');
  }

  public function participations() {
    return $this->hasMany('Participation','idu');
  }

}
