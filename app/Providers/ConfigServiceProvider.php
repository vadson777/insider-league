<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{

	public function register()
    {
		$this->setLocale();
		$this->setCarbonLocale();
	}

	private function setLocale()
    {
		setlocale(LC_TIME, match ($this->app->getLocale()) {
            'ru' => 'ru_RU.UTF-8',
            'pt' => 'pt_BR.UTF-8',
            default => 'en_US.UTF-8',
        });
	}

	private function setCarbonLocale()
    {
		$locale = $this->app->getLocale();
		Carbon::setLocale($locale);
	}

}
