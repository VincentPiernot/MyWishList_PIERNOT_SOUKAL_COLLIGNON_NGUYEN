<?php


namespace mywishlist\models;

class Messagepublic extends \Illuminate\Database\Eloquent\Model {

  protected $table = 'messagePublic';
  protected $primaryKey = 'idM';

  public $timestamps = false;

  public function liste() {
    return $this->belongsTo('Liste',
    'no') ;
  }

}
