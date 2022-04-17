<?php

namespace App\Providers;

use Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{

	public function boot()
    {
        Blade::directive('set', function($data) {
            list($var, $val) = explode(',', $data, 2);
            return '<?'.'php ${'.$var.'} = '.trim($val).'; ?>';
        });
	}

}
