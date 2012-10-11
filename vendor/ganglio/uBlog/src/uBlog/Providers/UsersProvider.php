<?php

namespace uBlog\Providers;

use Silex\Application;
use Silex\ServiceProviderInterface;

class UsersProvider implements ServiceProviderInterface
{

	public function register(Application $app)
	{
		$app['users'] = $app->share(function () use ($app) {
			$UP = new Users($app);

			return $UP;
		});
	}

	public function boot(Application $app) {}
}
