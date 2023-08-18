<?php
Route::group(['middleware' => ['web', 'auth', 'roles'], 'roles' => ['admin'], 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\EnvatoIntegration\Http\Controllers'], function()
{
    Route::get('/mailbox/envato-setting/{id}', 'EnvatoCustomFieldsController@index')->name('mailboxes.envato_setting');
    Route::get('/mailbox/envato-setting/ajax-html/{action}', ['uses' => 'EnvatoCustomFieldsController@ajaxHtml', 'laroute' => true])->name('mailboxes.envato_setting.ajax_html');
    Route::post('/envato-setting/ajax-admin', ['uses' => 'EnvatoCustomFieldsController@ajaxAdmin', 'laroute' => true])->name('mailboxes.envato_setting.ajax_admin');
    Route::post('/envato-setting/ajax-admin-create', ['uses' => 'EnvatoCustomFieldsController@ajaxAdminCreate', 'laroute' => true])->name('mailboxes.envato_setting.ajax_admin_create');
     Route::post('/envato-setting/ajax-admin-delete', ['uses' => 'EnvatoCustomFieldsController@ajaxAdminDelete', 'laroute' => true])->name('mailboxes.envato_setting.ajax_admin_delete');
});

Route::group(['middleware' => ['web', 'auth', 'roles'], 'roles' => ['user', 'admin'], 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\EnvatoIntegration\Http\Controllers'], function()
{
    Route::post('/envato-setting/ajax', ['uses' => 'EnvatoCustomFieldsController@ajax', 'laroute' => true])->name('mailboxes.envato_setting.ajax');
    Route::get('/envato-setting/ajax-search', ['uses' => 'EnvatoCustomFieldsController@ajaxSearch', 'laroute' => true])->name('mailboxes.envatoSetting.ajax_search');
    Route::match(['post','get'],'/envatointegration/ajax', ['uses' => 'EnvatoCustomFieldsController@getOrders']);
});