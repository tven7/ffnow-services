<?php namespace abstractions\repositories\user;

use Illuminate\Support\ServiceProvider;
use Profile;

class UserServiceProvider extends ServiceProvider
{
        function register()
        {
                $this->app->bind(
      		'abstractions\repositories\user\UserRepositoryInterface',
      		'abstractions\repositories\user\EloquentUserRepository'
    		);
        }
}
