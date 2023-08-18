<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\Setemail\Http\Controllers'], function()
{
    Route::get('/', 'SetemailController@index');
});
