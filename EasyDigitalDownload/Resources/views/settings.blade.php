@extends('layouts.app')

@section('title_full', __('Easy Digital Downloads').' - '.$mailbox->name)

@section('sidebar')
    @include('partials/sidebar_menu_toggle')
    @include('mailboxes/sidebar_menu')
@endsection

@section('content')

    <div class="section-heading-noborder">
        {{ __('Easy Digital Downloads') }}
    </div>

   
 	<div class="row-container">
        <div class="row">
            <div class="col-xs-12">
               <form class="form-horizontal margin-top margin-bottom" method="POST" action="">
                {{ csrf_field() }}

                <div class="form-group {{ $errors->has('eddurls') ? ' has-error' : '' }}">
                    <label class="col-sm-2 control-label">{{ __('Store URL') }}</label>

                    <div class="col-sm-6">
                        <div class="input-group input-sized-lg">
                            <span class="input-group-addon input-group-addon-grey">https://</span>
                            <input type="text" class="form-control input-sized-lg" name="eddurls" value="{{ $mailbox->eddurls ? $mailbox->eddurls : '' }}" required="required">
                        </div>
                        @include('partials/field_error', ['field'=>'eddurls'])
                        <p class="form-help">
                            {{ __('Example') }}: example.org/shop/
                        </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">{{ __('API Consumer Key') }}</label>

                    <div class="col-sm-6">
                        <input type="text" class="form-control input-sized-lg" name="eddkey" value="{{ $mailbox->eddkey ? $mailbox->eddkey : '' }}" required="required">
                        <div class="{{ $errors->has('eddkey') ? ' has-error' : '' }}">
                                @include('partials/field_error', ['field'=>'eddkey'])
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">{{ __('API Consumer Token') }}</label>

                    <div class="col-sm-6">
                        <input type="text" class="form-control input-sized-lg" name="eddtoken" value="{{ $mailbox->eddtoken ? $mailbox->eddtoken : '' }}" required="required">
                        <div class="{{ $errors->has('token') ? ' has-error' : '' }}">
                            @include('partials/field_error', ['field'=>'eddtoken'])
                        </div>
                        
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">{{ __('API Version') }}</label>

                    <div class="col-sm-6">
                        
                        <div class="input-group input-sized-lg">
                            <span class="input-group-addon input-group-addon-grey">v</span>
                            <input type="number" class="form-control input-sized-lg" name="version" value="1">
                        </div>

                    </div>
                </div>

                <div class="form-group margin-top margin-bottom">
                    <div class="col-sm-6 col-sm-offset-2">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
  
@endsection

@include('partials/editor')

@section('javascript')
    @parent
    initEasyDigitalDownloadSettings('{{ __('Reset to default values?') }}');
@endsection