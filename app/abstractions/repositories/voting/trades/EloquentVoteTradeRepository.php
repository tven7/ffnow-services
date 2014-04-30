<?php namespace abstractions\repositories\voting\trade;

use Auth;

class EloquentVoteTradeRepository implements VoteTradeRepositoryInterface
{
	//Get a list of all scenarios paginated 
        public function getScenarioList($paginate=15)
        {
                $scenarios = TradeScenario::paginate($paginate)->orderBy('created_at')->get();
                $response = [];
                foreach($scenarios as $scenario)
                {
                        $response[$scenario->id] = $scenario->toArray();
                }
                return $response;
        }

	public function getVoting($scenario_id)
	{
		$count = TradeVote::where('scenario_id','=',$scenario_id)->count();
		$votes = TradeVote::where('scenario_id','=',$scenario_id)->take($count)->get();
		$tradeScenario= TradeScenario::where('scenario_id','=',$scenario_id)->take(1)->get();
		$owner=$tradeScenario[0]->user_id;
		$voting = [];
		$voting['owner']=$owner;
		$voting['scenario_id']=$votes[0]>scenario_id;
		$voting['total_votes']=$count;
		$voters = [];
		foreach($votes as $vote)
		{
			$voter_alias = User::where('id','=',$vote->voter_id)->take(1)->get();
			array_push($voters,$voter_alias);
			if(isset($team_vote_count[$vote->favored_team_id]) || array_key_exists($vote->favored_team_id,$team_vote_count))
			{
				$team_vote_count[$vote->favored_team_id] += 1;
			}else{
				$team_vote_count[$vote->favored_team_id] = 1;
			}
		}
		$voting['team_vote_count']=$team_vote_count;
		$voting['voters']=$voters;
		return $voting;
	}
        
	public function vote($input)
	{
		$id = $input['scenario_id'];
		$voter_id = $input['voter_id'];
		$tv = new TradeVote;
		$tv->scenario_id=$id;
		$tv->voter_id=$voter_id;
		$tv->favored_team_id=$input['favored_team_id'];
		$tv->favored_by=$input['favored_by'];
		try
		{
			$tv->save();
		 	return ["status"=>"OK" , "TradeVoteId:"=>$tv->id];
		}catch(Exception $e){
			Log::error($e->getMessage());
		}
		return ["status"=>"FAIL" , "TradeVoteException:Check Server Logs"];
	}

}
