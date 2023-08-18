<?php
Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\SidebarApi\Http\Controllers'], function()
{
    Route::get('/mailbox/sidebarapi-settings/{id}', ['uses' => 'SidebarApiController@settings', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.sidebarapi_settings');
    Route::post('/mailbox/sidebarapi-settings/{id}', ['uses' => 'SidebarApiController@settingsSave', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.sidebarapi_settings.save');
});