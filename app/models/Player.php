<?php

class Player extends Eloquent {

  protected $table ='player';

  public $timestamps  = true;

  protected $guarded = [];

  public static $rules = array();
  
  public function teams(){
	return	$this->hasManyThrough('Team','TeamPlayer');
  }
}
