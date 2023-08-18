<?php

namespace Modules\SidebarApi\Http\Controllers;

use App\Mailbox;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\SidebarApi\Entities\SidebarApi;
use Validator;

class SidebarApiController extends Controller
{
    /**
     * Edit Settings.
     * @return Response
     */
    public function settings($id)
    {   
        $mailbox    = Mailbox::findOrFail($id);
        $SidebarApis = SidebarApi::where('mailbox_id',$id)->first();
        return view('sidebarapi::settings', ['mailbox' => $mailbox,'SidebarApis'=>$SidebarApis]);
    }

    public function settingsSave($id, Request $request)
    {   
        $sidebarApi            = SidebarApi::where('mailbox_id',$id)->first();
        if($sidebarApi){
            $sidebarApi->url   = $request->url;
            $sidebarApi->mailbox_id   = $request->mailbox_id;
            $sidebarApi->save();
        }else{
            $sidebarApiNew = new SidebarApi();
            $sidebarApiNew->url       = $request->url;
            $sidebarApiNew->mailbox_id   = $request->mailbox_id;
            $sidebarApiNew->save();
        }
        \Session::flash('flash_success_floating', __('Settings updated'));
        return redirect()->route('mailboxes.sidebarapi_settings', ['id' => $id]);
    }

   
}
