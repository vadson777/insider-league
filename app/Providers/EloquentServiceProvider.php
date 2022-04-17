<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class EloquentServiceProvider extends ServiceProvider
{

	public function boot()
    {
		$this->registerMorphMap();

		//Model::preventLazyLoading(!$this->app->isProduction());
	}

	public function register()
    {

	}

	protected function registerMorphMap()
    {
		Relation::enforceMorphMap([

		]);
	}

}
