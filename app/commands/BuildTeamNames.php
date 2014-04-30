<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
#use Player, TeamPlayer, Team;
class BuildTeamNames extends Command {
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'team:names';

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
		$mfl_player_url = 'http://football.myfantasyleague.com/2014/export?TYPE=players&L=&W=&JSON=1';
		$player_types_wanted = ['Def'=>'1'];
		$response_decoded = json_decode($this->http_get($mfl_player_url));
		#var_dump($response_decoded);
		$players = $response_decoded->players->player;
		$team_names;
		foreach($players as $player)
		{
			#print $player->name."\t".$player->position."\t".$player->team."\t".$player->id;
			#print "\n";
			if(array_key_exists($player->position,$player_types_wanted))
			{
				list($city,$teamName)=explode(',',$player->name);
				print "'$player->team' => '$city',\n";
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
