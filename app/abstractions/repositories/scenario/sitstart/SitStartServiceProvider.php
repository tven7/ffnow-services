<?php namespace abstractions\repositories\scenario\sitstart;

use Illuminate\Support\ServiceProvider;

class SitStartServiceProvider extends ServiceProvider
{
        function register()
        {
                $this->app->bind(
      		'abstractions\repositories\scenario\sitstart\SitStartRepositoryInterface',
      		'abstractions\repositories\scenario\sitstart\EloquentSitStartRepository'
    		);
        }
}
