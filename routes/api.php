<?php

Route::redirect('/', config('app.url'));

Route::group(['middleware' => ['api']], function() {

});
