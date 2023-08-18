<?php

namespace Modules\EasyDigitalDownload\Http\Controllers;

use App\Mailbox;
use App\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Validator;

class EasyDigitalDownloadController extends Controller
{
    /**
     * Edit Settings.
     * @return Response
     */
    public function settings($id)
    {
        $mailbox = Mailbox::findOrFail($id);
        
        return view('easydigitaldownload::settings', ['mailbox' => $mailbox]);
    }

    public function settingsSave($id, Request $request)
    {
        $mailbox            = Mailbox::findOrFail($id);
        $mailbox->eddurls   =   $request->eddurls;
        $mailbox->eddkey    =   $request->eddkey;
        $mailbox->eddtoken  =   $request->eddtoken;
        $mailbox->save();

        \Session::flash('flash_success_floating', __('Settings updated'));

        return redirect()->route('mailboxes.easydigitaldownload_settings', ['id' => $id]);
    }

   
}
