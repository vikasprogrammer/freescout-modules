

<div class="form-group">
    <label class="col-sm-4 control-label">{{ __('Choose custom field for purchase code') }}</label>

    <div class="col-sm-8">
    	<select name="type" class="form-control" @if ($mode != 'create') disabled @endif>
    		@foreach (\Modules\EnvatoIntegration\Entities\EnvatoCustomField::$types as $type_key => $type_name)
    			<option value="{{ $type_key }}" @if ($type_key == $custom_field->type) selected @endif>{{ __($type_name) }}</option>
    		@endforeach
    	</select>
    </div>
</div>



<div class="form-group">
    <label class="col-sm-4 control-label">{{ __('Envato API key') }}</label>

    <div class="col-sm-8">
        <input class="form-control" name="name" value="{{ $custom_field->name }}" maxlength="75" required/>
    </div>
</div>