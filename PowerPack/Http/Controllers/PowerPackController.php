<?php

namespace Modules\PowerPack\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Mailbox;
use DB;
use Modules\PowerPack\Entities\PowerPack;
use Illuminate\Support\Facades\Schema;
class PowerPackController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('powerpack::index');
    }

    public function settings($id)
    {   
        if(Schema::hasTable('custom_fields')){
            $custom_fields=DB::table('custom_fields')->where(['mailbox_id'=>$id])->whereIn('type',array(1, 2))->get();
        }else{
            $custom_fields=[];
        }
        $powerPack=PowerPack::where('mailbox_id',$id)->first();
        $mailbox = Mailbox::findOrFail($id);
        return view('powerpack::settings', ['mailbox' => $mailbox,'powerPack'=>$powerPack,'custom_fields'=>$custom_fields]);
    }
    public function saveSettingEup(Request $request,$id)
    { 
        $powerPack=PowerPack::where('mailbox_id',$id)->first();
        if(!empty($powerPack)){
            $exitPowerPack = PowerPack::find($powerPack->id);
            if($request->contact_window_css){
                $contact_window_css=1;
            }else{
                $contact_window_css=0;
            }
            if($request->custom_html){
                $custom_html=1;
            }else{
                $custom_html=0;
            }
            if($request->enable_text_logo){
                $enable_text_logo=1;
            }else{
                $enable_text_logo=0;
            }
            if($request->enable_kb_section){
                $enable_kb_section=1;
            }else{
                $enable_kb_section=0;
            }

            if($request->file('eupLogoImage')){
                $filenameWithExt = $request->file('eupLogoImage')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('eupLogoImage')->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$extension;
                $baseUrl=public_path('img');
                $path = $request->file('eupLogoImage')->move($baseUrl,$fileNameToStore);
                $exitPowerPack->eupLogoImage = $fileNameToStore;
            }
            $exitPowerPack->contact_window_css=$contact_window_css;
            $exitPowerPack->nav_bg_for_user_end_portal=$request->userEndBgColor;
            $exitPowerPack->active_menu_item_bg_user_end_portal=$request->activeMenuItmeUserEndBgColor;
            $exitPowerPack->add_css_user_end_portal=$request->add_css_user_end_portal;
            $exitPowerPack->end_btn_bg_color=$request->end_btn_bg_color;
            $exitPowerPack->end_text_color=$request->end_text_color;
            $exitPowerPack->custom_html=$custom_html;
            $exitPowerPack->enable_text_logo=$enable_text_logo;
            $exitPowerPack->enable_kb_section=$enable_kb_section;
            $exitPowerPack->eupLogoText=$request->eupLogoText;
            $exitPowerPack->textarea_field_text=$request->textarea_field_text;
            $exitPowerPack->number_of_category_kb=$request->number_of_category_kb;
            $exitPowerPack->number_of_article_kb=$request->number_of_article_kb;
            $exitPowerPack->custom_fields=json_encode($request->custom_field_id);
            $exitPowerPack->save();
        }else{
            if($request->contact_window_css){
                $contact_window_css=1;
            }else{
                $contact_window_css=0;
            }
            if($request->custom_html){
                $custom_html=1;
            }else{
                $custom_html=0;
            }
            if($request->enable_kb_section){
                $enable_kb_section=1;
            }else{
                $enable_kb_section=0;
            }
            $powerPackNew = new PowerPack();
            $powerPackNew->mailbox_id=$id;
            $powerPackNew->contact_window_css=$contact_window_css;
            $powerPackNew->nav_bg_for_user_end_portal=$request->userEndBgColor;
            $powerPackNew->active_menu_item_bg_user_end_portal=$request->activeMenuItmeUserEndBgColor;
            if($request->enable_text_logo){
                $enable_text_logo=1;
            }else{
                $enable_text_logo=0;
            }
            if($request->file('eupLogoImage')){
                $filenameWithExt = $request->file('eupLogoImage')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('eupLogoImage')->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$extension;
                $baseUrl=public_path('img');
                $path = $request->file('eupLogoImage')->move($baseUrl,$fileNameToStore);
                $powerPackNew->eupLogoImage = $fileNameToStore;
            }
            $powerPackNew->enable_text_logo=$enable_text_logo;
            $powerPackNew->eupLogoText=$request->eupLogoText;
            $powerPackNew->end_btn_bg_color=$request->end_btn_bg_color;
            $powerPackNew->end_text_color=$request->end_text_color;
            $powerPackNew->custom_html=$custom_html;
            $powerPackNew->enable_kb_section=$enable_kb_section;
            $powerPackNew->add_css_user_end_portal=$request->add_css_user_end_portal;
            $powerPackNew->textarea_field_text=$request->textarea_field_text;
            $powerPackNew->number_of_category_kb=$request->number_of_category_kb;
            $powerPackNew->number_of_article_kb=$request->number_of_article_kb;
            $powerPackNew->custom_fields=json_encode($request->custom_field_id);
            $powerPackNew->save();
        }
        return redirect()->route('mailboxes.powerpack', ['id'=>$id])->with('message', 'Setting Updated !');
    }
    public function saveSettingKb(Request $request,$id)
    {   
        $powerPack=PowerPack::where('mailbox_id',$id)->first();
        if(!empty($powerPack)){

            $exitPowerPack = PowerPack::find($powerPack->id);
            if($request->contact_window_email_prefilled_kb){
                $contact_window_email_kb=1;
            }else{
                $contact_window_email_kb=0;
            }
            if($request->kb_enable_text_logo){
                $kb_enable_text_logo=1;
            }else{
                $kb_enable_text_logo=0;
            }
            if($request->file('kbLogoImage')){
                $filenameWithExt = $request->file('kbLogoImage')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('kbLogoImage')->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$extension;
                $baseUrl=public_path('img');
                $path = $request->file('kbLogoImage')->move($baseUrl,$fileNameToStore);
                $exitPowerPack->kbLogoImage = $fileNameToStore;
            }
            $exitPowerPack->kb_enable_text_logo=$kb_enable_text_logo;
            $exitPowerPack->kbLogoText=$request->kbLogoText;
            $exitPowerPack->contact_window_email_prefilled_kb=$contact_window_email_kb;
            $exitPowerPack->nav_bg_for_kb_portal=$request->kbPortalBgColor;
            $exitPowerPack->active_menu_item_bg_kb_portal=$request->activeMenuItmekbPortalBgColor;
            $exitPowerPack->bk_btn_bg_color=$request->bk_btn_bg_color;
            $exitPowerPack->kb_text_color=$request->kb_text_color;
            $exitPowerPack->add_css_kb_portal=$request->add_css_kb_portal;
            $exitPowerPack->save();
        }else{
            if($request->contact_window_email_prefilled_kb){
                $contact_window_email_kb=1;
            }else{
                $contact_window_email_kb=0;
            }
            $powerPackNew = new PowerPack();
            $powerPackNew->mailbox_id=$id;
             if($request->kb_enable_text_logo){
                $kb_enable_text_logo=1;
            }else{
                $kb_enable_text_logo=0;
            }
            if($request->file('kbLogoImage')){
                $filenameWithExt = $request->file('kbLogoImage')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('kbLogoImage')->getClientOriginalExtension();
                $fileNameToStore = $filename.'_'.time().'.'.$extension;
                $baseUrl=public_path('img');
                $path = $request->file('kbLogoImage')->move($baseUrl,$fileNameToStore);
                $powerPackNew->kbLogoImage = $fileNameToStore;
            }
            $powerPackNew->kb_enable_text_logo=$kb_enable_text_logo;
            $powerPackNew->kbLogoText=$request->kbLogoText;
            $powerPackNew->contact_window_email_prefilled_kb=$contact_window_email_kb;
            $powerPackNew->nav_bg_for_kb_portal=$request->kbPortalBgColor;
            $powerPackNew->active_menu_item_bg_kb_portal=$request->activeMenuItmekbPortalBgColor;
            $powerPackNew->bk_btn_bg_color=$request->bk_btn_bg_color;
            $powerPackNew->kb_text_color=$request->kb_text_color;
            $powerPackNew->add_css_kb_portal=$request->add_css_kb_portal;
            $powerPackNew->save();
        }
        return redirect()->route('mailboxes.powerpack', ['id'=>$id])->with('messageKB', 'Setting Updated !');
    }
    public function chatSetting(Request $request ,$id){
        if($request->isMethod('post')){
            $checkPowerPackExit=PowerPack::where('mailbox_id',$id)->count();
            if($checkPowerPackExit>0){
                $powerPack=PowerPack::where('mailbox_id',$id)->first();
                $updateChatSetting=PowerPack::find($powerPack->id);
                $updateChatSetting->minutes=$request->minutes;
                $updateChatSetting->chat_message=$request->chat_message;
                $updateChatSetting->second=$request->second;
                $updateChatSetting->save();
            }else{
                $newChatSetting=new PowerPack();
                $newChatSetting->minutes=$request->minutes;
                $newChatSetting->chat_message=$request->chat_message;
                $newChatSetting->second=$request->second;
                $newChatSetting->save();
            }
           
        }
       return redirect()->route('mailboxes.powerpack', ['id'=>$id])->with('messageChat', 'Setting Updated !');
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('powerpack::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('powerpack::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('powerpack::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
