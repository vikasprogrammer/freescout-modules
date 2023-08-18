<?php

namespace Modules\EasyDigitalDownload\Http\Controllers;
use App\Mailbox;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Session;

class EasyDigitalDownloadOrdersController extends Controller
{
    /**
     * Ajax controller.
     */
    public function ajax(Request $request)
    {   

        $easyKey = Session::get('mailbox')['eddkey'];
        $easyToken = Session::get('mailbox')['eddtoken'];
        $url =$this->getSanitizedUrl(Session::get('mailbox')['eddurls']);
        
        $response = [
            'status' => 'error',
            'msg'    => '', 
        ];
        if(empty($easyKey) || empty($easyToken) || empty($url) || $easyKey==null || $easyToken==null || $url==null){
            $orders = '';
            $response['html'] = \View::make('easydigitaldownload::partials/orders_list', [
                'orders'         => $orders,
                'customer_email' => $request->customer_email,
                'load'           => false,
                'url'            => '',
            ])->render();
            $response['status'] = 'success';
            return \Response::json($response);
        }
        switch ($request->action) {

            case 'orders':
                $response['html'] = '';

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url.'edd-api/sales/?key='.$easyKey.'&token='.$easyToken.'&email='.$request->customer_email);
                    
                    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $json = curl_exec($ch);
                    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    $result = json_decode($json, true);
                    if(isset($result['request_speed']) && empty($result['sales'])){

                        $orders = '';
                        $response['html'] = \View::make('easydigitaldownload::partials/orders_list', [
                            'orders'         => $orders,
                            'customer_email' => $request->customer_email,
                            'load'           => false,
                            'url'            => $url,
                        ])->render();
                        $response['status'] = 'success';
                        return \Response::json($response);
                    }
                    if (!empty($result['error'])) {
                        \Log::error('[EasyDigital] '.$result['error']);
                    } elseif(isset($result['sales'])) {
                        if (is_array($result['sales'])) {
                            $orders = $result['sales'];
                            \Cache::put('wc_orders_'.$request->customer_email, $orders, now()->addMinutes(60));

                            $response['html'] = \View::make('easydigitaldownload::partials/orders_list', [
                                'orders'         => $orders,
                                'customer_email' => $request->customer_email,
                                'load'           => false,
                                'url'            => $url,
                            ])->render();
                        }
                        
                    }
               
                $response['status'] = 'success';
                break;

            default:
                $response['msg'] = 'Unknown action';
                break;
        }

        if ($response['status'] == 'error' && empty($response['msg'])) {
            $response['msg'] = 'Unknown error occured';
        }

        return \Response::json($response);
    }
    public static function getSanitizedUrl($eddurl)
    {

        $eddurl = preg_replace("/https?:\/\//i", '', $eddurl);

        if (substr($eddurl, -1) != '/') {
            $eddurl .= '/';
        }

        return 'https://'.$eddurl;
    }
}
