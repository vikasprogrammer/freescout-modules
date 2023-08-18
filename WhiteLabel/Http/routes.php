<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\WhiteLabel\Http\Controllers'], function()
{
    Route::get('/', 'WhiteLabelController@index');
    Route::match(['get','post'],'/mailbox/whitelabel/{id}', ['uses' => 'WhiteLabelController@settings', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.whitelabel');
});
