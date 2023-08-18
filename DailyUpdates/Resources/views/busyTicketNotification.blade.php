@extends('layouts.app')

@section('title', __('Busy Ticket Notification'))

@section('content')
  <div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default panel-wizard">
                <div class="panel-body">

                    <div class="wizard-header1">
                        <h3> {{ __('Busy Ticket Notification') }}</h3>
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
                                              <label for="busy_ticket_notification">{{ __('Busy Ticket Notification Times') }}:</label>
                                             <input type="text" class="form-control input-sized-lg" name="times" value="{{$busyTicketNotification?$busyTicketNotification->times:''}}" required="required">
                                               @if($errors->has('busy_ticket_notification'))
                                                    <span class="text-danger">{{ $errors->first('busy_ticket_notification') }}</span>
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
