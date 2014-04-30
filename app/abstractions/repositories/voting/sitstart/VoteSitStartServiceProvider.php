<?php namespace abstractions\repositories\voting\sitstart;

use Illuminate\Support\ServiceProvider;

class VoteSitStartServiceProvider extends ServiceProvider
{
        function register()
        {
                $this->app->bind(
      		'abstractions\repositories\voting\sitstart\VoteSitStartRepositoryInterface',
      		'abstractions\repositories\voting\sitstart\EloquentVoteSitStartRepository'
    		);
        }
}
