<?php
Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\AutoSignature\Http\Controllers'], function()
{
    Route::get('/mailbox/auto-signature/{id}', 'AutoSignatureController@index')->name('mailboxes.auto_signature');
    Route::get('/mailbox/auto-signature/ajax-html/{action}', ['uses' => 'AutoSignatureController@ajaxHtml', 'laroute' => true])->name('mailboxes.auto_signature.ajax_html');
    Route::post('/mailbox/auto-signature/ajax', ['uses' => 'AutoSignatureController@ajax', 'laroute' => true])->name('mailboxes.auto_signature.ajax');
});