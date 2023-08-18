<?php
Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\EasyDigitalDownload\Http\Controllers'], function()
{
    Route::get('/mailbox/easydigitaldownload-settings/{id}', ['uses' => 'EasyDigitalDownloadController@settings', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.easydigitaldownload_settings');
    Route::post('/mailbox/easydigitaldownload-settings/{id}', ['uses' => 'EasyDigitalDownloadController@settingsSave', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.easydigitaldownload_settings.save');
    Route::post('/easydigitaldownload/ajax', ['uses' => 'EasyDigitalDownloadOrdersController@ajax', 'laroute' => true])->name('easydigitaldownload.ajax');
});