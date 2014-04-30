<?php namespace abstractions\repositories\scenario\sitstart;

use Auth;

class EloquentSitStartRepository implements SitStartRepositoryInterface{

	/*
	 * Create a new Scenario passing in type, user_id, subject and scenario input
	 */ 
	const MY_TEAM= 'my_team';	
	private $check_input_fail_message;
	
	public function getScenario($id)
	{
		$scenario['scenario']=SitStartScenario::where('id','=',$id)->take(1)->get();
                $count=CommonScenarioPlayer::where('scenario_id','=',$id)->count();
                $scenario['players']=CommonScenarioPlayer::where('scenario_id','=',$id)->take($count)->get();
                return $scenario;

	}
	public function create($input)
	{
	    $checkInput = $this->checkForInput($input);
	    if($checkInput != 1)
		return ["status"=>"FAIL" , "info"=>$this->check_input_fail_message];
	    try
	    {
		$scenario_id = $this->createSitStartScenario($input);
	    }catch(Exception $e){
		Log.e("EloquentScenarioRepository.create: ".$e.getMessage());
		return ["status"=>"FAIL" , "info"=>"SitStartCreateException"];
	    }
		return ["status"=>"OK" , "SitStartId:"=>$scenario_id];
	}

	private function createSitStartScenario($input)
	{
		$scenario = new SitStartScenario;
		$scenario->user_id=$input['user_id'];
		$scenario->sub=$input['sub'];
		$scenario->start=$input['start'];
		$scenario->week=$input['week'];
		$scenario->save();
		$this->createSitStartScenarioPlayers($scenario->id,$input);
		return $scenario->id;
	}

	private function createSitStartScenarioPlayers($scenario_id,$input)
	{
		if(array_has_keys('my_teams_players', $input))
		{
			// Params shoudl be sent as &my_team_players=345,431,692
			$my_player_ids = explode(',',$input['my_teams_players']);
			foreach($my_player_ids as $my_player_id)
			{
			 	$this->savePlayer($scenario_id,$my_player_id,MY_TEAM);
			}
		}
	}

	private function savePlayer($scenario_id,$player_id,$owner)
	{
		$csp = new CommonScenarioPlayer;
		$csp->scenario_id=$scenario_id;
		$csp->player_id = $player_id;
		$csp->owner = $owner;
		$csp->save();
	}

	//TODO: Check for key and value to make sure necessary input exists
	private function checkForInput($input){
		if(!array_key_exists('user_id',$input))
		{
			 Log.e("SitStart missing user_id");
			 $this->check_input_fail_message = "user_id is required";
			 return 0;
		}
		if(!array_key_exists('sub',$input))
		{
			 Log.e("SitStart Scenario missing subject");
			 $this->check_input_fail_message = "subject is required";
			 return 0;
		}
		if(!array_key_exists('my_teams_players',$input) )
		{	
			 Log.e("SitStart missing user's players ");
			 $this->check_input_fail_message = "need user's team player(s) to be passed in as param";
			 return 0;
		}
		return 1;
	}
}
