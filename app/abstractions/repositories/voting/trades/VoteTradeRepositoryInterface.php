<?php 
namespace abstractions\repositories\voting\trade;

interface VoteTradeRepositoryInterface{
	public function getVoting($scenario_id);
	public function vote($input);
}
