<?php namespace abstractions\repositories\voting\trade;

use Illuminate\Support\ServiceProvider;

class VoteTradeServiceProvider extends ServiceProvider
{
        function register()
        {
                $this->app->bind(
      		'abstractions\repositories\voting\trade\VoteTradeRepositoryInterface',
      		'abstractions\repositories\voting\trade\EloquentVoteTradeRepository'
    		);
        }
}
