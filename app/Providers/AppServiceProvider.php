<?php

namespace App\Providers;

use DB;
use App;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
	    if(!App::isLocal()) {
		    DB::connection()->disableQueryLog();
	    }

	    JsonResource::withoutWrapping();
	    Paginator::useBootstrapFive();

		RedirectResponse::macro('withMessage', function(string $text, string $type = 'info') {
			/** @var RedirectResponse $this */
			return $this->with('message', compact('type', 'text'));
		});
    }

}
