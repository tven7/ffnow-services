<?php namespace abstractions\repositories\scenario\trade;

use Illuminate\Support\ServiceProvider;
use Profile;

class TradeScenarioServiceProvider extends ServiceProvider
{
        function register()
        {
                $this->app->bind(
      		'abstractions\repositories\scenario\TradeScenarioRepositoryInterface',
      		'abstractions\repositories\scenario\EloquentTradeScenarioRepository'
    		);
        }
}
