<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\PowerPack\Http\Controllers'], function()
{
    Route::get('/', 'PowerPackController@index');
    Route::match(['get','post'],'/mailbox/powerpack/{id}', ['uses' => 'PowerPackController@settings', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.powerpack');
    Route::match(['get','post'],'/mailbox/powerpack-save-setting-kb/{id}', ['uses' => 'PowerPackController@saveSettingKb', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.powerpack.save.settings.kb');
    Route::match(['get','post'],'/mailbox/powerpack-save-setting-eup/{id}', ['uses' => 'PowerPackController@saveSettingEup', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.powerpack.save.settings.eup');
    Route::match(['get','post'],'/mailbox/powerpack-chat-setting/{id}', ['uses' => 'PowerPackController@chatSetting', 'middleware' => ['auth', 'roles'], 'roles' => ['admin']])->name('mailboxes.powerpack.chat.settings');
});
