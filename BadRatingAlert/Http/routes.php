<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\BadRatingAlert\Http\Controllers'], function()
{
    //Route::get('/', 'BadRatingAlertController@index');
    Route::match(['get','post'],'/mailbox/badratingalert/{id}', 'BadRatingAlertController@index')->name('mailboxes.badratingalert');
});
