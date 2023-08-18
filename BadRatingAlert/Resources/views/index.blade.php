@extends('layouts.app')

@section('title_full', __('Bad Rating Alert').' - '.$mailbox->name)

@section('sidebar')
    @include('partials/sidebar_menu_toggle')
    @include('mailboxes/sidebar_menu')
@endsection

@section('content')

    <div class="section-heading-noborder">
        {{ __('Bad Rating Alert') }}
    </div>

    <div class="row-container">
        <div class="row">
            <div class="col-xs-12">
               <form class="form-horizontal margin-top margin-bottom" method="POST" action="">
                {{ csrf_field() }}

                <div class="form-group {{ $errors->has('slack_url') ? ' has-error' : '' }}">
                    <label class="col-sm-2 control-label">{{ __('Slack Url') }}</label>

                    <div class="col-sm-6">
                        <div class="input-group input-sized-lg">
                            <input type="text" class="form-control input-sized-lg" name="slack_url" value="{{$badRatingAlert?$badRatingAlert->slack_url:''}}" required="required">
                        </div>
                        @include('partials/field_error', ['field'=>'slack_url'])

                    </div>
                </div>
                <div class="form-group {{ $errors->has('slack_url') ? ' has-error' : '' }}">
                    <label class="col-sm-2 control-label">{{ __('Ratings') }}</label>

                    <div class="col-sm-6">
                        <label class="checkbox-inline">
                          <input type="checkbox" value="1" name="rating_great" @if($badRatingAlert) @if($badRatingAlert->rating_great==1) checked @else ' ' @endif @endif>Great
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" value="2" name="rating_okay" @if($badRatingAlert) @if($badRatingAlert->rating_okay==2) checked @else ' ' @endif @endif>Okay
                        </label>
                        <label class="checkbox-inline">
                          <input type="checkbox" value="3" name="rating_not_okay" @if($badRatingAlert) @if($badRatingAlert->rating_not_okay==3) checked @else ' ' @endif @endif>Not Good
                        </label>
                    </div>
                </div>
                <div class="form-group {{ $errors->has('enable_slack_notification') ? ' has-error' : '' }}">
                    <label class="col-sm-2 control-label" for="enable_slack_notification">{{ __('Enable Slack Notification') }}</label>
                    <div class="col-sm-6">
                        <input type="checkbox" name="enable_slack_notification" class="" id="enable_slack_notification" value="1" @if($badRatingAlert) @if($badRatingAlert->enable_slack_notification==1) checked @else ' ' @endif @endif >
                         @include('partials/field_error', ['field'=>'enable_slack_notification'])
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
