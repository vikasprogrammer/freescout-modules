<div class="row-container">
	<form class="form-horizontal" method="POST" action="">

		<div class="form-group">
	        <label class="col-md-1 control-label">{{ __('Name') }}</label>

	        <div class="col-md-11">
	            <input class="form-control" name="name" maxlength="75" />
	        </div>
	    </div>

		<div class="form-group">
	        <label class="col-md-1 control-label">{{ __('Reply') }}</label>

	        <div class="col-md-11 new-saved-reply-editor">
	            <textarea class="form-control" name="text" rows="8">{{ $text }}</textarea>
	        </div>
	    </div>

		<div class="form-group margin-top margin-bottom-10 ">
	        <div class="col-md-11 col-md-offset-1">
	            <button type="button" class="btn btn-primary new-saved-reply-save" data-loading-text="{{ __('Saving') }}…">{{ __('Save Signature') }}</button>
	        </div>
	    </div>
	</form>
</div>