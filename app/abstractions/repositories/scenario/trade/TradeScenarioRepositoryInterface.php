<?php 
namespace abstractions\repositories\scenario\trade;

interface ScenarioRepositoryInterface{
	public function getScenario($id);
	public function create($input);
}
