<?php 
namespace abstractions\repositories\scenario\sitstart;

interface SitStartRepositoryInterface{
	public function getScenario($id);
	public function create($input);
}
