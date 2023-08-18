@extends('layouts.app')

@section('title_full', __('SidebarApi').' - '.$mailbox->name)

@section('sidebar')
    @include('partials/sidebar_menu_toggle')
    @include('mailboxes/sidebar_menu')
@endsection

@section('content')

    <div class="section-heading-noborder">
        {{ __('Sidebar Api') }}
    </div>

    <div class="row-container">
        <div class="row">
            <div class="col-xs-12">
               <form class="form-horizontal margin-top margin-bottom" method="POST" action="">
                {{ csrf_field() }}

                <div class="form-group {{ $errors->has('url') ? ' has-error' : '' }}">
                    <label class="col-sm-2 control-label">{{ __('URL') }}</label>

                    <div class="col-sm-6">
                        <div class="input-group input-sized-lg">
                            <span class="input-group-addon input-group-addon-grey">https://</span>
                            @if(isset($SidebarApis))
                                <input type="text" class="form-control input-sized-lg" name="url" value="{{ $SidebarApis->url ? $SidebarApis->url : '' }}" required="required">
                                 <input type="hidden" class="form-control input-sized-lg" name="mailbox_id" value="{{$mailbox->id}}" required="required">
                            @else
                                <input type="hidden" class="form-control input-sized-lg" name="mailbox_id" value="{{$mailbox->id}}" required="required">
                                <input type="text" class="form-control input-sized-lg" name="url" value="" required="required">
                            @endif
                        </div>
                        @include('partials/field_error', ['field'=>'url'])
                       
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
    initSidebarApiSettings('{{ __('Reset to default values?') }}');
@endsection