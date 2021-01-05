<?php


namespace mywishlist\models;

class Participation extends \Illuminate\Database\Eloquent\Model {

  protected $table = 'Participation';
  protected $primaryKey = ['item_id','nom'];
  public $incrementing = false;

  public $timestamps = false;

  protected function setKeysForSaveQuery(\Illuminate\Database\Eloquent\Builder  $request) {
              return $request->where('item_id', $this->getAttribute('item_id'))->where('nomP', $this->getAttribute('nomP'));
  }

  public function user() {
    return $this->belongsTo('User',
    'idu') ;
  }

}
