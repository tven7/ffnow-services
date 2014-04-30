<?php

class TradeScenario extends Eloquent {

  protected $table ='trade_scenario';

  public $timestamps  = true;

  protected $guarded = [];

  public static $rules = array();
 
  public function draft_picks()
  {
    return $this->hasMany('TradeScenarioDraftPick');
  }
  public function players()
  {
	return  $this->hasMany('CommonScenarioPlayer');
  }
  public function trade_votes()
  {
    return $this->hasMany('TradeVote');
  }
}
