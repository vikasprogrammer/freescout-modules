<ul class="sr-dropdown-list hidden">
	@foreach ($saved_replies as $saved_reply)
		<li data-id="{{ $saved_reply->id }}">{{ $saved_reply->name }}</li>
	@endforeach
	<li data-id="divider"></li>
	<li>{{ __('Save This Reply') }}…</li>
</ul>