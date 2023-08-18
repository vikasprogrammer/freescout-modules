<?php

namespace Modules\EnvatoIntegration\Http\Controllers;

use App\Conversation;
use App\Mailbox;
use Modules\EnvatoIntegration\Entities\EnvatoCustomField;
use Modules\EnvatoIntegration\Entities\ConversationEnvato;
use Modules\EnvatoIntegration\Entities\CustomField;
use Modules\EnvatoIntegration\Entities\ConversationCustomFiledWithEnvato;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Session;
class EnvatoCustomFieldsController extends Controller
{
    /**
     * Display a listing of the resource.
     */ 
    public function index($id)
    {  
        $mailbox = Mailbox::findOrFail($id);
        $custom_fields = CustomField::getMailboxCustomFields($id);
        $envato_custom_fields = EnvatoCustomField::where('mailbox_id',$id)->first();
        return view('envatointegration::index', [
            'mailbox'       => $mailbox,
            'custom_fields' => $custom_fields,
            'envato_custom_fields' => $envato_custom_fields,
        ]);
    }

    public function get_random_user_agent() {
        $arr = array(
            'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0',
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0',
            'Mozilla/5.0 (Windows NT 6.1; rv:27.3) Gecko/20130101 Firefox/27.3',
            'Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201',
            'Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9.2.3) Gecko/20100401 Lightningquail/3.6.3',
            'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1944.0 Safari/537.36',
            'Opera/12.80 (Windows NT 5.1; U; en) Presto/2.10.289 Version/12.02',
            'Mozilla/5.0 (Windows NT 6.0; rv:2.0) Gecko/20100101 Firefox/4.0 Opera 12.14'
        );
        return $arr[rand(0,count($arr) - 1)];
    }
    
    public function ajaxAdminCreate(Request $request){
        $customField        = new EnvatoCustomField();
        $custom_field_name  = CustomField::find($request->custom_field_id);
        $customField->custom_field_name  = $custom_field_name->name;
        $customField->name  = $request->name;
        $customField->mailbox_id = $request->mailbox_id;
        $customField->required = 0;
        $customField->options  = json_encode($request);
        $customField->custom_fields_id=$request->custom_field_id;
        $customField->sort_order = 1;
        $customField->save();
        $lastId=$customField->id;
        $custom_fields=EnvatoCustomField::find($lastId);
        return redirect()->back()->with('successMsg', 'Envato API key saved.');

    }
    public function ajaxAdminDelete(Request $request){
        if($request->id){
            EnvatoCustomField::find($request->id)->delete();
            return redirect()->back()->with('errorMsg', 'Envato API key deleted.');
        }
        
    }
    public function getOrders(Request $request){
        if($request->conversion_id){
            $response = [
                'status' => 'error',
                'msg'    => '', // this is error message
            ];
            $customerData       = Conversation::find($request->conversion_id);
            $conversatioData    = ConversationCustomFiledWithEnvato::where(['conversation_id'=>$request->conversion_id])->first();
            if($conversatioData){
                if(empty($conversatioData->value)){
                    $response['msg'] = 'Please enter purchase code';
                }
                $envatoApiKey=EnvatoCustomField::where(['mailbox_id'=>$customerData->mailbox_id,'custom_fields_id'=>$conversatioData->custom_field_id ])->first();
                if($envatoApiKey){
                    if(empty($envatoApiKey->name)){
                        $response['msg'] = 'Please configuration envato module';
                    }
                }
                if(!empty($customerData) && !empty($conversatioData) && !empty($envatoApiKey)){      
                    $url = 'https://api.envato.com/v3/market/author/sale?code=' . urlencode($conversatioData->value);
                    $userAgent = $this->get_random_user_agent();
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Authorization: Bearer ' . $envatoApiKey->name
                    ));
                    $data = curl_exec($ch);
                    curl_close($ch);
                    if(!empty($data)) {
                    $result = json_decode($data, true);

                     \Cache::put('envato_orders'.$customerData->customer_email, $result, now()->addMinutes(60));                  } 
                    if (!empty($result['error'])) {
                    \Log::error('[envatointegration] '.$result['error']);
                    } elseif (is_array($result)) {
                        $response['html'] = \View::make('envatointegration::partials/orders_list', [
                                'orders'         => $result,
                                'load'           => false,    
                            ])->render();
                        $response['status'] = 'success';
                    }
                }else{
                    $response['msg'] = 'Unknown action';
                }
            }
           
          return \Response::json($response);
        }
        
    }
     /**
     * Ajax search.
     */
    public function ajaxSearch(Request $request)
    {
        $response = [
            'results'    => [],
            'pagination' => ['more' => false],
        ];

        $query = ConversationCustomFiledWithEnvato::select('value')
            ->where('custom_field_id', $request->custom_field_id)
            ->where('value', 'like', '%'.$request->q.'%')
            ->orderBy('value')
            ->groupBy('value');

        $custom_fields = $query->paginate(20);

        foreach ($custom_fields as $row) {
            $response['results'][] = [
                'id'   => $row->value,
                'text' => $row->value,
            ];
        }

        $response['pagination']['more'] = $custom_fields->hasMorePages();

        return \Response::json($response);
    }
}
