<?php namespace abstractions\repositories\voting\sitstart;

use Auth;

class EloquentVoteSitStartRepository implements VoteSitStartRepositoryInterface
{
	//Get a list of all scenarios paginated	
	public function getScenarioList($paginate=15)
	{
		$scenarios = SitStartScenario::paginate($paginate)->orderBy('created_at')->get();
		$response = [];
		foreach($scenarios as $scenario)
		{
			$response[$scenario->id] = $scenario->toArray();
		}	
		return $response;
	}

	public function getVoting($scenario_id)
	{
		if(!$scenario_id)
		{
			return ["status"=>"FAIL" , "getVoting: Missing scenario_id"];
		}
		$count = SitStartVote::where('scenario_id','=',$scenario_id)->count();
		$votes = SitStartVote::where('scenario_id','=',$scenario_id)->take($count)->get();
		$sitStartScenario= SitStartScenario::where('scenario_id','=',$scenario_id)->take(1)->get();
		$owner=$sitStartScenario[0]->user_id;
		$voting = [];
		$voting['owner']=$owner;
		$voting['scenario_id']=$votes[0]>scenario_id;
		$voting['total_votes']=$count;
		$voters = [];
		$player_votes = [];
		foreach($votes as $vote)
		{
			$voter_alias = User::where('id','=',$vote->voter_id)->take(1)->get();
			array_push($voters,$voter_alias);
			if(isset($player_votes[$vote->player_id]) || array_key_exists($vote->player_id,$player_votes))
			{
				$player_votes[$vote->player_id] += 1;
			}else{
				$player_votes[$vote->player_id] = 1;
			}
		}
		$voting['player_votes']=$player_votes;
		$voting['voters']=$voters;
		return $voting;
	}
        
	public function vote($input)
	{
		$id = $input['scenario_id'];
		$player_ids = $input['player_ids'];
		$voter_user_id = $input['voter_user_id'];
		if(!$id)
		{
			return ["status"=>"FAIL" , "SitStartVoteException: Missing scenario_id"];
		}
		if(!isset($player_ids[0]) || !$player_ids[0])
		{
			return ["status"=>"FAIL" , "SitStartVoteException: Missing players list"];

		}
		if(!$voter_user_id)
		{
			return ["status"=>"FAIL" , "SitStartVoteException: Missing voter id"];
		}
		foreach($player_ids as $player_id)
		{
			$ssv = new SitStartVote;
			$ssv->scenario_id=$id;
			$ssv->voter_user_id=$voter_user_id;
			$ssv->player_id=$player_id;
			try
			{
				$ssv->save();
		 		return ["status"=>"OK" , "SitStartVoteId:"=>$ssv->id];
			}catch(Exception $e){
				Log::error($e->getMessage());
			}
		}
		return ["status"=>"FAIL" , "SitStartVoteException:Check Server Logs"];
	}
}
