<?php

namespace Modules\BadRatingAlert\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Mailbox;
use Modules\BadRatingAlert\Entities\BadRatingAlert;
class BadRatingAlertController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request,$id)
    {   
        if($request->isMethod('post')){
            $checkBadRatingAlert=BadRatingAlert::where('mailbox_id',$id)->count();
            if($checkBadRatingAlert>0){

                if($request->enable_slack_notification){
                    $enable=$request->enable_slack_notification;
                }else{
                    $enable=0;
                }
                $updateBadRatingAlert =BadRatingAlert::where('mailbox_id',$id)->first();
                $updateBadRatingAlert->mailbox_id=$id;
                $updateBadRatingAlert->slack_url=$request->slack_url;
                $updateBadRatingAlert->rating_great=$request->rating_great;
                $updateBadRatingAlert->rating_okay=$request->rating_okay;
                $updateBadRatingAlert->rating_not_okay=$request->rating_not_okay;
                $updateBadRatingAlert->enable_slack_notification=$enable;
                $updateBadRatingAlert->save();
            }else{

                if($request->enable_slack_notification){
                    $enable=$request->enable_slack_notification;
                }else{
                    $enable=0;
                }
                $newBadRatingAlert =new BadRatingAlert();
                $newBadRatingAlert->mailbox_id=$id;
                $newBadRatingAlert->slack_url=$request->slack_url;
                $newBadRatingAlert->rating_great=$request->rating_great;
                $newBadRatingAlert->rating_okay=$request->rating_okay;
                $newBadRatingAlert->rating_not_okay=$request->rating_not_okay;
                $newBadRatingAlert->enable_slack_notification=$enable;
                $newBadRatingAlert->save();
            }
        }
        $badRatingAlert =BadRatingAlert::where('mailbox_id',$id)->first();
        $mailbox = Mailbox::findOrFail($id);
        return view('badratingalert::index',compact('mailbox','badRatingAlert'));
    }

}
