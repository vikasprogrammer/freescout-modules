<li @if (Route::is('mailboxes.envato_setting'))class="active"@endif><a href="{{ route('mailboxes.envato_setting', ['id'=>$mailbox->id]) }}"><i class="glyphicon glyphicon-leaf"></i> {{ __('Envato') }}</a></li>