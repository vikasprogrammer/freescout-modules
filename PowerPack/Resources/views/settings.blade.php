@extends('layouts.app')

@section('title_full', __('Power Pack').' - '.$mailbox->name)

@section('sidebar')
    @include('partials/sidebar_menu_toggle')
    @include('mailboxes/sidebar_menu')
@endsection

@section('content')
    <link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>
    <style type="text/css">
        .eup-d-none{
            display: none;
        }
        .kb-d-none{
            display: none;
        }
        .eup-textarea-none{
            display: none;
        }
    </style>
    <div class="section-heading-noborder">
        {{ __('Power Pack') }}
    </div>
    <div class="row-container">
        @if (\Session::has('message'))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> {!! \Session::get('message') !!}
          </div>
        @endif
        @if (\Session::has('messageKB'))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> {!! \Session::get('messageKB') !!}
          </div>
        @endif
        @if (\Session::has('messageChat'))
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Success!</strong> {!! \Session::get('messageChat') !!}
          </div>
        @endif
        <ul class="nav nav-tabs">
            <li class="@if(!\Session::has('messageKB')) @if(\Session::has('message')) active @else active @endif @endif"><a data-toggle="tab" href="#enduserportal">End User Portal</a></li>
            @if(Schema::hasTable('kb_categories'))
                <li class="@if(\Session::has('messageKB')) active @endif"><a data-toggle="tab" href="#knowledgebase">Knowledge Base</a></li>
            @endif
            <li><a data-toggle="tab" href="#chatSetting">Chat Setting</a></li>
          </ul>
            <div class="tab-content">
                <div id="enduserportal" class="tab-pane fade in active">
                  <form class="form-horizontal margin-top margin-bottom" method="POST" action="{{ route('mailboxes.powerpack.save.settings.eup',['id'=>$mailbox->id]) }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="multiSelection">{{ __('Select Custom Fields') }}</label>
                        <div class="col-sm-6" >
                            <select data-placeholder="Select Custom Fields" multiple class="chosen-select form-control" name="custom_field_id[]" id="multiSelection">
                                <option value=""></option>
                                @foreach($custom_fields as $custom_field)
                                    @php
                                    if($powerPack  && json_decode($powerPack->custom_fields)){
                                        $exitCustomField=in_array($custom_field->id,json_decode($powerPack->custom_fields));
                                    }else{
                                      $exitCustomField='';  
                                    }
                                    @endphp
                                    <option value="{{ $custom_field->id }}" @if($exitCustomField) selected @endif>{{ $custom_field->name }}</option>
                                @endforeach
                              </select>
                              <span style="color: gray;">single field and dropdown fields are supported</span>
                        </div>
                       
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="upgradeCss">{{ __('Email Prefilled') }}</label>

                        <div class="col-sm-6">
                            <input type="checkbox" name="contact_window_css" class="" id="upgradeCss" value="1" @if($powerPack) @if($powerPack->contact_window_css==1) checked @else ' ' @endif @endif >
                            <span><a href="https://scoutdevs.com/how-to-prefill-email-in-end-user-portal/" style="color: gray;" target="_blank">View Docs</a></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="customHtml">{{ __('Enable textarea') }}</label>

                        <div class="col-sm-6">
                            <input type="checkbox" name="custom_html" class="" id="customHtml" value="1" @if($powerPack) @if($powerPack->custom_html==1) checked @else ' ' @endif @endif >
                        </div>
                    </div>
                    <div class="form-group eup-textarea-none displayEupTextareaText">
                        <label class="col-sm-3 control-label" for="eupTextareaText">{{ __('Textarea Text') }}</label>
                        <div class="col-sm-6" >
                           <textarea class="form-control"  id="eupTextareaText" name="textarea_field_text">{{ $powerPack? $powerPack->textarea_field_text : '' }}</textarea>
                           
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kbNumberOfCategory">{{ __('Number of category in KB') }}</label>
                        <div class="col-sm-6" >
                            <input type="number" name="number_of_category_kb" id="kbNumberOfCategory" class="form-control" value="{{ $powerPack? $powerPack->number_of_category_kb : '' }}">
                           
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kbNumberOfArticle">{{ __('Number of Article in KB') }}</label>
                        <div class="col-sm-6" >
                          <input type="number" name="number_of_article_kb" id="kbNumberOfArticle" class="form-control" value="{{ $powerPack? $powerPack->number_of_article_kb : '' }}">
                           
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="userEndBgColor">{{ __('Navbar backgroud Color') }}</label>
                        <div class="col-sm-6">
                            <input type="color" id="userEndBgColor" name="userEndBgColor" value="{{ $powerPack? $powerPack->nav_bg_for_user_end_portal : '#0068bd' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="activeMenuItmeUserEndBgColor">{{ __('Active Navbar Item backgroud Color') }}</label>

                        <div class="col-sm-6">
                            <input type="color" id="activeMenuItmeUserEndBgColor" name="activeMenuItmeUserEndBgColor" value="{{ $powerPack? $powerPack->active_menu_item_bg_user_end_portal : '#0068bd' }}">
                        </div>
                    </div>
                     <div class="form-group">
                        <label class="col-sm-3 control-label" for="EndBtnBgColor">{{ __('Button backgroud Color') }}</label>
                        <div class="col-sm-6">
                            <input type="color" id="EndBtnBgColor" name="end_btn_bg_color" value="{{ $powerPack? $powerPack->end_btn_bg_color?$powerPack->end_btn_bg_color:'#0068bd' : '#0068bd' }}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="EndTextColor">{{ __('Button Text Color') }}</label>
                        <div class="col-sm-6">
                            <input type="color" id="EndTextColor" name="end_text_color" value="{{ $powerPack? $powerPack->end_text_color? $powerPack->end_text_color: '#ffffff': '#ffffff' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="eupLogoImage">{{ __('Logo') }}</label>
                        <div class="col-sm-6" >
                            <input type="file" name="eupLogoImage" class="form-control" id="eupLogoImage">
                            @if($powerPack)
                                @if($powerPack->eupLogoImage)
                                @php
                                    $imageEup=$powerPack?$powerPack->eupLogoImage:'';
                                @endphp
                                <img src="{{ asset('img/'.$imageEup) }}" height="50px">
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="enableEupLogoText">{{ __('Enable Logo Text') }}</label>
                        <div class="col-sm-6" >
                            <input type="checkbox" name="enable_text_logo" class="" id="enableEupLogoText" value="1" @if($powerPack) @if($powerPack->enable_text_logo==1) checked @else ' ' @endif @endif >
                        </div>
                    </div>
                     <div class="form-group eup-d-none displayEupLogoText">
                        <label class="col-sm-3 control-label" for="eupLogoText">{{ __('Logo Text') }}</label>
                        <div class="col-sm-6" >
                           
                            <input type="text" name="eupLogoText" class="form-control" id="eupLogoText" value="{{ $powerPack? $powerPack->eupLogoText : '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="enableKbSection">{{ __('Enable KB Section') }}</label>
                        <div class="col-sm-6" >
                            <input type="checkbox" name="enable_kb_section" class="" id="enableKbSection" value="1" @if($powerPack) @if($powerPack->enable_kb_section==1) checked @else ' ' @endif @endif >
                        </div>
                    </div>
                   
                   
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="add_css_user_end_portal">{{ __('Custom Css') }}</label>

                        <div class="col-sm-6">
                            <textarea name="add_css_user_end_portal" id="add_css_user_end_portal" class="form-control" cols="12" rows="6">{{ $powerPack? $powerPack->add_css_user_end_portal : '' }}</textarea>
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
            <div id="knowledgebase" class="tab-pane fade">
                  <form class="form-horizontal margin-top margin-bottom" method="POST" action="{{ route('mailboxes.powerpack.save.settings.kb',['id'=>$mailbox->id]) }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="upgradeCss">{{ __('Email Prefilled') }}</label>
                        <div class="col-sm-6">
                            <input type="checkbox" name="contact_window_email_prefilled_kb" class="" id="upgradeCss" value="1" @if($powerPack) @if($powerPack->contact_window_email_prefilled_kb==1) checked @else ' ' @endif @endif >
                            <span><a href="https://scoutdevs.com/how-to-prefill-email-in-end-user-portal/" style="color: gray;" target="_blank">View Docs</a></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kbPortalBgColor">{{ __('Navbar backgroud Color') }}</label>

                        <div class="col-sm-6">
                            <input type="color" id="kbPortalBgColor" name="kbPortalBgColor" value="{{ $powerPack? $powerPack->nav_bg_for_kb_portal : '#0068bd' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="activeMenuItmekbPortalBgColor">{{ __('Active Navbar Item backgroud Color') }}</label>

                        <div class="col-sm-6">
                            <input type="color" id="activeMenuItmekbPortalBgColor" name="activeMenuItmekbPortalBgColor" value="{{ $powerPack? $powerPack->active_menu_item_bg_kb_portal : '#0068bd' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="KbBtnBgColor">{{ __('Button backgroud Color') }}</label>
                        <div class="col-sm-6">
                            <input type="color" id="KbBtnBgColor" name="bk_btn_bg_color" value="{{ $powerPack? $powerPack->bk_btn_bg_color : '#0068bd' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="KbTextColor">{{ __('Button Text Color') }}</label>
                        <div class="col-sm-6">
                            <input type="color" id="KbTextColor" name="kb_text_color" value="{{ $powerPack? $powerPack->kb_text_color? $powerPack->kb_text_color: '#ffffff': '#ffffff' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kbLogoImage">{{ __('Logo') }}</label>
                        <div class="col-sm-6" >
                            <input type="file" name="kbLogoImage" class="form-control" id="kbLogoImage">
                            @if($powerPack)
                                @if($powerPack->kbLogoImage)
                                @php
                                    $imageEup=$powerPack?$powerPack->kbLogoImage:'';
                                @endphp
                                <img src="{{ asset('img/'.$imageEup) }}" height="50px">
                                @endif
                            @endif
                        </div>
                       
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="enableKbLogoText">{{ __('Enable Logo Text') }}</label>
                        <div class="col-sm-6" >
                            <input type="checkbox" name="kb_enable_text_logo" class="" id="enableKbLogoText" value="1" @if($powerPack) @if($powerPack->kb_enable_text_logo==1) checked @else ' ' @endif @endif >
                        </div>
                    </div>
                    <div class="form-group kb-d-none displayKbLogoText">
                        <label class="col-sm-3 control-label" for="kbLogoText">{{ __('Logo Text') }}</label>
                        <div class="col-sm-6" >
                           
                            <input type="text" name="kbLogoText" class="form-control" id="kbLogoText" value="{{ $powerPack? $powerPack->kbLogoText : '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="add_css_kb_portal">{{ __('Custom Css') }}</label>

                        <div class="col-sm-6">
                            <textarea name="add_css_kb_portal" id="add_css_kb_portal" class="form-control" cols="12" rows="6">{{ $powerPack? $powerPack->add_css_kb_portal : '' }}</textarea>
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
            <div id="chatSetting" class="tab-pane fade">
                  <form class="form-horizontal margin-top margin-bottom" method="POST" action="{{ route('mailboxes.powerpack.chat.settings',['id'=>$mailbox->id]) }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="minutes">{{ __('Select Minutes') }}</label>
                        <div class="col-sm-6">
                            
                            <select class="form-control" name="minutes" id="minutes">
                                <option value=""></option>
                                <?php for($i=1;$i<=30;$i++){ 
                                    if($powerPack){
                                        if($powerPack->minutes==$i){
                                            $selected='selected';
                                        }else{
                                            $selected='';
                                        }
                                    }else{
                                        $selected='';
                                    }
                                    echo  '<option value='.$i.' '.$selected.'>'.$i.' Minute</option>';
                                }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="second">{{ __('Select Seconds') }}</label>
                        <div class="col-sm-6">
                            
                            <select class="form-control" name="second" id="second">
                                <option value=""></option>
                                <?php for($i = 5; $i <= 60; $i += 5){ 
                                    if($powerPack){
                                        if($powerPack->second==$i){
                                            $selected='selected';
                                        }else{
                                            $selected='';
                                        }
                                    }else{
                                        $selected='';
                                    }
                                    echo  '<option value='.$i.' '.$selected.'>'.$i.' Seconds</option>';
                                }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="chat_message">{{ __('Message') }}</label>

                        <div class="col-sm-6">
                            <textarea name="chat_message" id="chat_message" class="form-control" cols="12" rows="6">{{ $powerPack? $powerPack->chat_message : '' }}</textarea>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>
    <script type="text/javascript">
        $(".chosen-select").chosen({
          no_results_text: "Oops, nothing found!"
        })
        $(document).ready(function(){
            $('body').on('click','#enableEupLogoText',function(){
                if ($(this).is(":checked"))
                {
                  $('.displayEupLogoText').removeClass('eup-d-none');
                }else{
                   $('.displayEupLogoText').addClass('eup-d-none');
                }
            });
            $('body').on('click','#enableKbLogoText',function(){
                if ($(this).is(":checked"))
                {
                  $('.displayKbLogoText').removeClass('kb-d-none');
                }else{
                   $('.displayKbLogoText').addClass('kb-d-none');
                }
            });
            $('body').on('click','#customHtml',function(){
                if ($(this).is(":checked"))
                {
                  $('.displayEupTextareaText').removeClass('eup-textarea-none');
                }else{
                   $('.displayEupTextareaText').addClass('eup-textarea-none');
                }
            });
            if ($('#enableEupLogoText').is(":checked"))
            {
                $('.displayEupLogoText').removeClass('eup-d-none');
            }
            if ($('#enableKbLogoText').is(":checked"))
            {
                $('.displayKbLogoText').removeClass('kb-d-none');
            }
            if($('#customHtml').is(":checked")){
                $('.displayEupTextareaText').removeClass('eup-textarea-none');
            }
        });
    </script>
@endsection
