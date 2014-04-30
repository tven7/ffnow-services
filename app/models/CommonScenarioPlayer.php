<?php

class CommonScenarioPlayer extends Eloquent {

  protected $table ='scenario_player';

  public $timestamps  = true;

  protected $guarded = [];

  public static $rules = array();
 
  public function players(){
	$this->hasMany('Player');
  }
}
