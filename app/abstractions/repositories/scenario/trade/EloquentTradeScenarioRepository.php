<?php namespace abstractions\repositories\scenario\trade;

use Auth;

class EloquentTradeScenarioRepository implements TradeScenarioRepositoryInterface{

	/*
	 * Create a new Scenario passing in type, user_id, subject and scenario input
	 */ 
	const MY_TEAM= 'my_team';	
	const OTHER_TEAM= 'other_team';	
	private $check_input_fail_message;
	
	public function getScenario($id)
	{
		$scenario['scenario']=SitStartScenario::where('id','=',$id)->take(1)->get();
		$count=CommonScenarioPlayer::where('scenario_id','=',$id)->count();
		$scenario['players']=CommonScenarioPlayer::where('scenario_id','=',$id)->take($count)->get();
		$count = TradeScenarioDraftPicks::where('scenario_id','=',$id)->count();
		$scenario['picks']=TradeScenarioDratPicks::where('scenario_id','=',$id)->take($count)->get();
		return $scenario;
	}
	public function create($input)
	{
	    $checkInput = $this->checkForInput($input);
	    if($checkInput != 1)
		return ["status"=>"FAIL" , "info"=>$this->check_input_fail_message];
	    try
	    {
		$scenario_id = $this->createTradeScenario($input);
	    }catch(Exception $e){
		Log.e("EloquentScenarioRepository.create: ".$e.getMessage());
		return ["status"=>"FAIL" , "info"=>"TradeScenarioCreateException"];
	    }
		return ["status"=>"OK" , "TradeScenarioId:"=>$scenario_id];
	}

	private function createTradeScenario($input)
	{
		$scenario = new TradeScenario;
		$scenario->user_id=$input['user_id'];
		$scenario->type=$input['type'];
		$scenario->sub=$input['sub'];
		$scenario->save();
		$this->createTradeScenarioDraftPicks($scenario->id,$input);
		$this->createTradeScenarioPlayers($scenario->id,$input);
		return $scenario->id;
	}

	private function createTradeScenarioPlayers($scenario_id,$input)
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
		if(array_has_keys('other_teams_players',$input))
		{
			$other_player_ids = explode(',',$input['other_teams_players']);
			foreach($other_player_ids as $other_player_id)
			{
			 	$this->savePlayer($scenario_id,$other_player_id,OTHER_TEAM);
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
	private function createTradeScenarioDraftPicks($scenario_id,$input)
	{
		if(array_has_keys('my_teams_draft_picks',$input)) 
		{
			$my_teams_draft_picks = explode(',',$input['my_teams_draft_picks']);	
			foreach($my_team_draft_picks as $myTeamsDraft)
			{
				// Since we are doing GET reqeusts we are expecting app to send Draft Picks param as follows
				// &my_team_draft=ROOKIE:1.1,LEFTOVER_FA:5.6,ALL_PLAYERS:3.1,STARTUP_NO_ROOKIES:3.4
				list($myTeamsDraftType,$myTeamsPick) = explode(':',$myTeamsDraft);
				$this->persistDraftPick($myTeamsDraftType,$myTeamsPick,$scenario_id,MY_TEAM);
			}
		}
		
		if(array_has_keys('other_team_draft_picks',$input))
                {
                	$other_teams_draft_picks = explode(',',$input['other_teams_draft_picks']);
			foreach($other_teams_draft_picks as $otherTeamsDraft)
                        {
				list($otherTeamsDraftType,$otherTeamsPick) = explode(':',$otherTeamsDraft);
				$this->persistDraftPick($otherTeamsDraftType,$otherTeamsPick,$scenario_id,OTHER_TEAM);
			}
		}

	}

	private function  persistDraftPick($type,$pick,$scenario_id,$owner)
	{
		$tsdp= new TradeScenarioDraftPick;
                $tsdp->scenario_id=$scenario_id;
                $tsdp->draft_pick=$pick;
                $tsdp->draft_type=$type;
                $tsdp->owner=$owner;
                $tsdp->save();
	}

	//TODO: Check for key and value to make sure necessary input exists
	private function checkForInput($input){
		if(!array_key_exists('user_id',$input))
		{
			 Log.e("TradeScenario missing user_id");
			 $this->check_input_fail_message = "user_id is required";
			 return 0;
		}
		if(!array_key_exists('sub',$input))
		{
			 Log.e("TradeScenario missing subject");
			 $this->check_input_fail_message = "subject is required";
			 return 0;
		}
		if(!array_key_exists('my_teams_players',$input) && !array_key_exists('my_teams_draft_picks',$input))
		{	
			 Log.e("TradeScenario missing user's players and draft_picks");
			 $this->check_input_fail_message = "either user's team player(s) or draft pick(s) need to be passed in as param";
			 return 0;
		}
		if(!array_key_exists('other_teams_players',$input) && !array_key_exists('other_teams_draft_picks',$input))
		{	
			 Log.e("TradeScenario missing other team's players and draft_picks");
			 $this->check_input_fail_message = "either other team's player(s) or draft pick(s) are needed";
			 return 0;
		}
		return 1;
	}
}
