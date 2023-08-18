@extends('layouts.app')

@section('title', __('Daily Updates'))

@section('content')
  <div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-wizard">
                <div class="panel-body">

                    <div class="wizard-header1">
                        <h3> {{ __('Daily Updates') }}</h3>
                    </div>
                    <div class="wizard-body">
                        @include('partials/flash_messages')

                        <div class="row">
                            <div class="col-xs-12">

                                <form class="margin-bottom" method="POST" action="" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                    <div class="row">
                                         <div class="col-xs-12 col-md-12">
                                            <div class="form-group">
                                              <label for="slack_url">{{ __('Slack Url') }}:</label>
                                             <input type="text" class="form-control input-sized-lg" name="slack_url" value="{{$dailyUpdate?$dailyUpdate->slack_url:''}}" required="required">
                                               @if($errors->has('slack_url'))
                                                    <span class="text-danger">{{ $errors->first('slack_url') }}</span>
                                                @endif
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-md-12">
                                            <div class="form-group">
                                              <label for="enable_slack_notification">{{ __('Enable Slack Notification') }}:</label><br>
                                             <input type="checkbox" name="enable_slack_notification" class="" id="enable_slack_notification" value="1" @if($dailyUpdate) @if($dailyUpdate->enable_slack_notification==1) checked @else ' ' @endif @endif>
                                              @if($errors->has('enable_slack_notification'))
                                                    <span class="text-danger">{{ $errors->first('enable_slack_notification') }}</span>
                                                @endif
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-md-12">
                                            <div class="form-group">
                                              <label for="">{{ __('Users') }}:</label><br>
                                               @if($users)
                                                @foreach($users as $user)
                                                <?php
                                                if($dailyUpdate){
                                                    if(isset($dailyUpdate->mailboxes) && $dailyUpdate->users !='null'){
                                                        if(in_array($user->id,json_decode($dailyUpdate->users))){
                                                        $checked='checked';
                                                        }else{
                                                            $checked='';
                                                        }
                                                    }else{
                                                        $checked='';
                                                    } 
                                                }else{
                                                    $checked='';
                                                }
                                                  
                                                ?>
                                                <label class="checkbox-inline">
                                                  <input type="checkbox" value="{{$user->id}}" name="users[]" {{$checked}}>{{$user->first_name.' '.$user->last_name}}
                                                </label>
                                                @endforeach
                                           @endif
                                            </div> 
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-xs-12 col-md-12">
                                            <div class="form-group">
                                              <label for="">{{ __('Mailboxs') }}:</label><br>
                                               @if($allMailboxs)
                                                    @foreach($allMailboxs as $allMailbox)
                                                    <?php 
                                                    if($dailyUpdate){
                                                       if(isset($dailyUpdate->mailboxes) && $dailyUpdate->mailboxes !='null'){
                                                            if(in_array($allMailbox->id,json_decode($dailyUpdate->mailboxes))){
                                                                $checkedMailbox='checked';
                                                            }else{
                                                                $checkedMailbox='';
                                                            }
                                                        }else{
                                                            $checkedMailbox='';
                                                        }  
                                                    }else{
                                                        $checkedMailbox='';
                                                    }
                                                      
                                                    ?>
                                                    <label class="checkbox-inline">
                                                      <input type="checkbox" value="{{$allMailbox->id}}" name="mailboxes[]" {{$checkedMailbox}}>{{$allMailbox->name}}
                                                    </label>
                                                    @endforeach
                                               @endif
                                            </div> 
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                                </form>
                            </div>
                           
                        </div>

                    </div>
                    <div class="wizard-footer">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>                
@endsection
