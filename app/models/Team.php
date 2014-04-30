<?php

class Team extends Eloquent {

  protected $table ='team';

  public $timestamps  = true;

  protected $guarded = [];

  public static $rules = array();

  public function players(){
	return $this->hasManyThrough('Player','TeamPlayer');
  }
}
