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
	protected $name = 'nfl::rosters';

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
	$siteUrl = "www.##.com/team/roster.html";
	$teams=['49ers','chargers','patriots','buffalobills','miamidolphins','newyorkjets','baltimoreravens','bengals','cleavelandbrowns','steelers',
		'houstontexans','colts','jaguars','titansonline','denverbroncos','kcchiefs','raiders','dallascowboys','giants','philadelphiaeagles',
		'redskins','chicagobears','detroitlions','packers','vikings','atlantafalcons','panthers','neworleansaints','buccaneers','azcardinals',
		'stlouisrams','seahawks'];

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
