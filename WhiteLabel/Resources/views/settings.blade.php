@extends('layouts.app')

@section('title_full', __('White Label'))

@section('sidebar')
    @include('partials/sidebar_menu_toggle')
    @include('mailboxes/sidebar_menu')
@endsection

@section('content')

    <div class="section-heading-noborder">
        {{ __('White Label') }}
    </div>

 	<div class="row-container">
        <div class="row">
            <div class="col-xs-12">
               <form class="form-horizontal margin-top margin-bottom" method="POST" action="" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group {{ $errors->has('user_email') ? ' has-error' : '' }}">
                    <label class="col-sm-3 control-label">{{ __('Except for this User Email') }}</label>

                    <div class="col-sm-6">
                        
                            @if(isset($userEmail))
                                <input type="text" class="form-control" name="user_email" value="{{ $userEmail->user_email ? $userEmail->user_email : '' }}" required="required">
                            @else
                                <input type="text" class="form-control" name="user_email" value="" required="required">
                            @endif
                        
                        @include('partials/field_error', ['field'=>'url'])
                       
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="logo">{{ __('Upload Logo') }}</label>
                    <div class="col-sm-6" >
                        <input type="file" name="logo" class="form-control" id="logo">
                            @if($userEmail)
                                @if($userEmail->logo)
                                @php
                                    $logo=$userEmail?$userEmail->logo:'';
                                @endphp
                                 <input type="hidden" name="old_logo" value="{{$userEmail->logo}}">
                                <img src="{{ asset('img/'.$logo) }}" height="50px" style="padding-top: 5px;">
                                @endif
                            @endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="brandText">{{ __('Brand Text') }}</label>
                    <div class="col-sm-6" >
                       <input type="text" name="brand_text" id="brandText" class="form-control" value="{{ $userEmail?$userEmail->brand_text ? $userEmail->brand_text : '':'' }}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="copyrightText">{{ __('Copyright Text') }}</label>
                    <div class="col-sm-6" >
                        <textarea name="copyrightText" class="form-control">{{ $userEmail? $userEmail->copyrightText? $userEmail->copyrightText: '': '' }}</textarea>
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