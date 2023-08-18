<?php

namespace Modules\WhiteLabel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\User;
use Auth;
use App\Mailbox;
use DB;
use Modules\WhiteLabel\Entities\WhiteLabel;
use Illuminate\Support\Facades\Schema;
class WhiteLabelController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('whitelabel::index');
    }
    public function settings(Request $request,$id){
        $userId = Auth::id();
        if($request->isMethod('post')){
            $checkUserEmail=WhiteLabel::where('user_email',$request->user_email)->count();
            if($checkUserEmail>0){
                $updateUserEmail=WhiteLabel::where('user_email',$request->user_email)->first();
                if($request->file('logo')){
                    $filenameWithExt = $request->file('logo')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('logo')->getClientOriginalExtension();
                    $fileNameToStore = $filename.'_'.time().'.'.$extension;
                    $baseUrl=public_path('img');
                    $path = $request->file('logo')->move($baseUrl,$fileNameToStore);
                    $updateUserEmail->logo = $fileNameToStore;
                }elseif($request->old_logo){
                    $updateUserEmail->logo = $request->old_logo;
                }
                $updateUserEmail->copyrightText=$request->copyrightText;
                $updateUserEmail->brand_text=$request->brand_text;
                $updateUserEmail->user_email=$request->user_email;
                $updateUserEmail->save();
            }else{
                WhiteLabel::truncate();
                $newUserEmail=new WhiteLabel();
                if($request->file('logo')){
                    $filenameWithExt = $request->file('logo')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('logo')->getClientOriginalExtension();
                    $fileNameToStore = $filename.'_'.time().'.'.$extension;
                    $baseUrl=public_path('img');
                    $path = $request->file('logo')->move($baseUrl,$fileNameToStore);
                    $newUserEmail->logo = $fileNameToStore;
                }elseif($request->old_logo){
                    $newUserEmail->logo = $request->old_logo;
                }
                $newUserEmail->copyrightText=$request->copyrightText;
                $newUserEmail->brand_text=$request->brand_text;
                $newUserEmail->user_email=$request->user_email;
                $newUserEmail->save();
            }
            $whiteLabel= new WhiteLabel();
        }
        $userEmail=WhiteLabel::first();
        $mailbox    = Mailbox::findOrFail($id);
        return view('whitelabel::settings',compact('userEmail','mailbox'));
    }
    
}
