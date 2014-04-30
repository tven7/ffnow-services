<?php
class EloquentSitStartTest extends TestCase
{
	private	$sitstart;
	private $scenario_id;
	public function setUp()
	{
		parent::setup();
		$this->sitstart = App::Make('abstraction\repositories\scenario\sitstart\EloquentSitStartRepository');
	}
	public function testCreate()
	{
		$input['user_id'] = 1;
		$input['sub'] = 'Do i sit calvin?';
		$input['start'] = 2;
		$input['week'] = 7;
		$input['my_team_players']="292,169,77";
		$status=$this->sitstart->create($input);
		$scenario_id=$status['SitStartId'];
		$this->assertEquals("OK",$status['status']);
	}
	public function testGetScenario()
	{
		if(!is_null($scenario_id))
		{
			$reply = $this->sitstart->getScenario($scenario_id);
			$scenario = $reply['scenario'];
			$this->assertEquals(1,$scenario->id);
		}
	}	
}
	
