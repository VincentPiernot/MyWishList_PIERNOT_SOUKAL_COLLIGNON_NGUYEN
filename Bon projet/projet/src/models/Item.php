<?php


namespace mywishlist\models;

class Item extends \Illuminate\Database\Eloquent\Model {
  protected $table = 'item';
  protected $primaryKey = 'id';

  public $timestamps = false;

  public function liste() {
    return $this->belongsTo('Liste',
    'no') ;
  }

  public function participations(){
    return $this->hasMany('participation','id');
  }

}
