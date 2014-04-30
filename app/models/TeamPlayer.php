<?php

class TeamPlayer extends Eloquent {

  protected $table ='team_player';

  public $timestamps  = true;

  protected $guarded = [];

  public static $rules = array();
}
