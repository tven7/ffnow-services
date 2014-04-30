<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
#use Player, TeamPlayer, Team;
class PullMFLPlayerStats extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'mfl';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
        {
		#$this->seed_players();
		$this->fill_player_profiles();
        }
  
	private function fill_player_profiles()
 	{
		$players = Player::all();
		$player_profile_url="http://football.myfantasyleague.com/2014/export?TYPE=playerProfile&JSON=1&P=";
		foreach($players as $playerObj){
			if($playerObj->id < 33) continue;
			$source_id = $playerObj->source_id;
			#$source_id='10933'; //this id is trouble some as it does not return dob property for $player obj
			$url = $player_profile_url.$source_id;
			print $url;print "\n";
		        $response = json_decode($this->http_get($url));
			$player = $response->playerProfile->player;
			$playerObj->weight=$player->weight;
			$playerObj->age = $player->age;
			$playerObj->height=$player->height;
			if(property_exists($player,dob))
				$playerObj->dob  = $player->dob;
			if(property_exists($player,adp))
				$playerObj->adp  = $player->adp;
			$playerObj->save();
			#break;
		}	
	}
	private function seed_players()
	{
		$mfl_player_url = 'http://football.myfantasyleague.com/2014/export?TYPE=players&L=&W=&JSON=1';
		$player_types_wanted = ['WR'=>'1','QB'=>'1','RB'=>'1','TE'=>'1','PK'=>'1','Def'=>'1','DT'=>'1','CB'=>'1','S'=>'1','DE'=>'1','LB'=>'1'];

		$team_name=['BUF' => 'Bills', 'IND' => 'Colts', 'MIA' => 'Dolphins', 'NEP' => 'Patriots', 'NYJ' => 'Jets', 'CIN' => 'Bengals', 'CLE' => 'Browns', 'TEN' => 'Titans', 'JAC' => 'Jaguars', 'PIT' => 'Steelers', 'DEN' => 'Broncos', 'KCC' => 'Chiefs', 'OAK' => 'Raiders', 'SDC' => 'Chargers', 'SEA' => 'Seahawks', 'DAL' => 'Cowboys', 'NYG' => 'Giants', 'PHI' => 'Eagles', 'ARI' => 'Cardinals', 'WAS' => 'Redskins', 'CHI' => 'Bears', 'DET' => 'Lions', 'GBP' => 'Packers', 'MIN' => 'Vikings', 'TBB' => 'Buccaneers', 'ATL' => 'Falcons', 'CAR' => 'Panthers', 'STL' => 'Rams', 'NOS' => 'Saints', 'SFO' => '49ers', 'BAL' => 'Ravens',
'HOU' => 'Texans','FA'=>'FA'];
		$team_city_state =['BUF' => ' Buffalo,NY', 'IND' => ' Indianapolis,IN', 'MIA' => ' Miami,FL', 'NEP' => ' New England,MA', 'NYJ' => ' New York,NY', 'CIN' => ' Cincinnati,OH', 'CLE' => ' Cleveland,OH', 'TEN' => ' Tennessee,TN', 'JAC' => ' Jacksonville,FL', 'PIT' => ' Pittsburgh,PA', 'DEN' => ' Denver,CO', 'KCC' => ' Kansas City,KS', 'OAK' => ' Oakland,CA', 'SDC' => ' San Diego,CA', 'SEA' => ' Seattle,WA', 'DAL' => ' Dallas,TX', 'NYG' => ' New York,NY', 'PHI' => ' Philadelphia,PA', 'ARI' => ' Arizona,AZ', 'WAS' => ' Washington,DC', 'CHI' => ' Chicago,IL', 'DET' => ' Detroit,MI', 'GBP' => ' Green Bay,WI', 'MIN' => ' Minnesota,MN', 'TBB' => ' Tampa Bay,FL', 'ATL' => ' Atlanta,GA', 'CAR' => ' Carolina,NC', 'STL' => ' St. Louis,MI', 'NOS' => ' New Orleans,LA', 'SFO' => ' San Francisco,CA', 'BAL' => ' Baltimore,MD', 'HOU' => ' Houston,TX','FA'=>'NA,NA'];
		$response_decoded = json_decode($this->http_get($mfl_player_url));
		#var_dump($response_decoded);
		$players = $response_decoded->players->player;
		foreach($players as $player)
		{
			#print $player->name."\t".$player->position."\t".$player->team."\t".$player->id;
			#print "\n";
			if(array_key_exists($player->position,$player_types_wanted))
			{
				print "player position found\n";
				$playerObj = new Player;
				list($last,$first)=explode(',',$player->name);
				$playerObj->first= trim($first);
				$playerObj->last= trim($last);
				$playerObj->source="mfl";
				$playerObj->source_id=$player->id;
				$playerObj->save();
				$city_state = $team_city_state[$player->team];

				list($city,$state) = explode(',',$city_state);

				//We want only 32 NFL Team entries not more. So dont insert duplicates
				$teamArr = Team::where('abbr','=',$player->team)->take(1)->get();
				$teamObj;
				
				if(sizeof($teamArr)==0)
				{
					$teamObj = new Team;
					$teamObj->abbr = $player->team;
					$teamObj->city=trim($city);
					$teamObj->state=trim($state);
					$teamObj->country_abbr = "US";
					$teamObj->name=$team_name[$player->team];
					$teamObj->save();
				}else{
					$teamObj = $teamArr[0];
				}
				$teamPlayerObj = new TeamPlayer;
				$teamPlayerObj->player_id=$playerObj->id;
				$teamPlayerObj->team_id=$teamObj->id;
				$teamPlayerObj->position=$player->position;
				$teamPlayerObj->current_team=true;
				$teamPlayerObj->save();
			}
		}
	}
	
	private function http_get($url)
	{
		$ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                $response= curl_exec($ch);

                switch(curl_errno($ch)){
                        case null: break; // When there is a valid response
                        case 28: Log::error("Search Query Timedout: $url\t$ch");
                                 $response=json_encode(["status"=>"Backend Timeout" , "info"=>$ch]);
                                 break;
                        default : Log::error("Error $ch");
                                 $response=json_encode(["status"=>"Backend Error" , "info"=>$ch]);
                                 break;
                }

                curl_close($ch);
		return $response;
	}
	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
