<?php

class SitStartScenario extends Eloquent {

  protected $table ='sitstart_scenario';

  public $timestamps  = true;

  protected $guarded = [];

  public static $rules = array();
 
  public function players()
  {
    return $this->hasMany('ScenarioPlayer');
  }

  public function sit_start_votes()
  {
    return $this->hasMany('SitStartVote');
  }
}
