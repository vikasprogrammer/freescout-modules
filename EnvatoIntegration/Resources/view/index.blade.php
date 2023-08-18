@extends('layouts.app')

@section('title_full', __('Envato Integration').' - '.$mailbox->name)

@section('body_attrs')@parent data-mailbox_id="{{ $mailbox->id }}"@endsection

@section('sidebar')
    @include('partials/sidebar_menu_toggle')
    @include('mailboxes/sidebar_menu')
@endsection

@section('content')

    <div class="section-heading">
        {{ __('Envato Integration') }}
    </div>
   	@if(session()->has('successMsg'))
    
	    <div class="alert alert-success alert-dismissible">
		    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		    <strong>Success!</strong> {{ session()->get('successMsg') }}
	  	</div>
    
	@endif
	@if(session()->has('errorMsg'))
    
		<div class="alert alert-danger alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong>Danger!</strong>  {{ session()->get('errorMsg') }}
		</div>
    
	@endif
    @if (count($custom_fields)>0 && $envato_custom_fields=='')
	<div class="row-container">
		<form class="form-horizontal new-custom-field-form" method="POST" action="{{ route('mailboxes.envato_setting.ajax_admin_create', ['action' => 'create']) }}" style="padding: 17px;">
			<div class="form-group">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

				<input type="hidden" name="mailbox_id" value="<?php echo $mailbox->id; ?>">
			    <label class="col-sm-4 control-label">{{ __('Choose custom field for purchase code') }}</label>

			    <div class="col-sm-8">
			    	<select name="custom_field_id" class="form-control">
			    		<option value="1" >{{ __('') }}</option>
			    		@foreach($custom_fields as $custom_field)
			    			<option value="{{$custom_field->id}}" >{{ __($custom_field->name) }}</option>
			    		@endforeach
			    	</select>
			    </div>
			</div>

			<div class="form-group">
			    <label class="col-sm-4 control-label">{{ __('Envato API key') }}</label>

			    <div class="col-sm-8">
			        <input class="form-control" name="name" value="" maxlength="75" required/>
			    </div>
			</div>

			<div class="form-group margin-top margin-bottom-10">
		        <div class="col-sm-10 col-sm-offset-2">
		            <button class="btn btn-primary" data-loading-text="{{ __('Saving') }}…">{{ __('Save Field') }}</button>
		        </div>
		    </div>
		</form>
	</div>
	@elseif(count($custom_fields)>0 && !empty($envato_custom_fields))

	<div class="row-container">
		<form class="form-horizontal new-custom-field-form" method="POST" action="{{ route('mailboxes.envato_setting.ajax_admin_delete', ['action' => 'delete']) }}" style="padding: 17px;">
			<div class="form-group">
				<input type="hidden" name="id" value="{{ $envato_custom_fields->id }}">
				<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
				<input type="hidden" name="mailbox_id" value="<?php echo $mailbox->id; ?>">
			    <label class="col-sm-4 control-label">{{ __('Choose custom field for purchase code') }}</label>

			    <div class="col-sm-8">
			    	<select name="custom_field_id" class="form-control" disabled="disabled">
			    		<option value="" >{{ __('') }}</option>
			    		
			    		<option value="{{ $envato_custom_fields->custom_fields_id }}" selected="selected">{{ __($envato_custom_fields->custom_field_name) }}</option>
			    		
			    	</select>
			    </div>
			</div>

			<div class="form-group">
			    <label class="col-sm-4 control-label">{{ __('Envato API key') }}</label>

			    <div class="col-sm-8">
			        <input class="form-control" name="name" value="{{ $envato_custom_fields->name }}" maxlength="75" required />
			    </div>
			</div>

			<div class="form-group margin-top margin-bottom-10">
		        <div class="col-sm-10 col-sm-offset-2">
		            <button class="btn btn-danger" data-loading-text="{{ __('Saving') }}…">{{ __('Delete Field') }}</button>
		        </div>
		    </div>
		</form>
	</div>
	@else
	<div class="row-container">
		<p class="errorMsgInstallModule">Please install or create custom field module</p>
	</div>
	@endif
	<style type="text/css">
		.errorMsgInstallModule{
		    font-size: 20px;
		    color: red;
		    text-align: center;
		    padding-top: 37px;
		}
	</style>
@endsection

@section('javascript')
    @parent
  
@endsection