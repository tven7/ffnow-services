<?php 
namespace abstractions\repositories\voting\sitstart;

interface VoteSitStartRepositoryInterface{
	public function getVoting($scenario_id);
	public function vote($input);
}
