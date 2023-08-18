<?php

Route::group(['middleware' => 'web', 'prefix' => \Helper::getSubdirectory(), 'namespace' => 'Modules\DailyUpdates\Http\Controllers'], function()
{
   // Route::get('/', 'DailyUpdatesController@index');
   Route::match(['get','post'],'/mailbox/dailyupdates','DailyUpdatesController@dailyupdates')->name('mailboxes.dailyupdates');
   Route::match(['get','post'],'/mailbox/busy/ticket/notification','DailyUpdatesController@mailboxBusyTicketNotification')->name('mailboxes.busy.ticket.notification');
   Route::match(['get','post'],'/users/mailboxes/dailyupdates','DailyUpdatesController@usersMailboxesDailyupdates')->name('users.mailboxes.dailyupdates');
   Route::match(['get','post'],'/customer/ticket/waiting','DailyUpdatesController@customerTicketWaiting')->name('customer.ticket.waiting');
   Route::match(['get','post'],'/busy/ticket/notification','DailyUpdatesController@busyTicketNotification')->name('busy.ticket.notification');
});
